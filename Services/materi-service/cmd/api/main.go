package main

import (
	"log"
	"materi-service/config"
	"materi-service/internal/delivery/http"
	"materi-service/internal/models"
	"materi-service/internal/repository"
	"materi-service/internal/usecase"
	"os"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func CORSMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Writer.Header().Set("Access-Control-Allow-Origin", "*")
		c.Writer.Header().Set("Access-Control-Allow-Credentials", "true")
		c.Writer.Header().Set("Access-Control-Allow-Headers", "Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization, accept, origin, Cache-Control, X-Requested-With")
		c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, OPTIONS, GET, PUT, DELETE")

		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}
		c.Next()
	}
}

// Fungsi Seeder untuk mengisi data awal jika kosong (Sinkron dengan Laravel Migration)
func SeedMaterials(db *gorm.DB) {
	log.Println("Checking for data seeds...")

	dataSeeds := map[int][]string{
		1: {"TIU", "Psychological Test", "Mathematics", "TWK", "TKP"},
		2: {"TIU", "Psychological Test", "Mathematics", "TWK", "TKP", "English", "Fisika", "Kimia"},
		3: {"Mathematics", "English", "Fisika", "Biology", "Chemistry", "Kimia"},
		4: {"Mathematics", "English", "Chemistry", "Biology", "Fisika", "Psychological Test"},
	}

	for classID, subjects := range dataSeeds {
		for _, s := range subjects {
			var exists bool
			// Cek apakah data sudah ada berdasarkan class_id dan nama materi
			db.Model(&models.Material{}).
				Select("count(*) > 0").
				Where("class_id = ? AND material_name = ?", classID, s).
				Find(&exists)

			if !exists {
				newMaterial := models.Material{
					ClassID:     classID,
					SubjectName: s,
					Title:       s + " Material",
					Week:        1,
				}
				db.Create(&newMaterial)
				log.Printf("Seeded: %s for Class %d", s, classID)
			}
		}
	}
}

func main() {
	// 1. Inisialisasi Database
	db := config.InitDB()

	// 2. Jalankan migrasi agar tabel materials otomatis terbuat/update di Postgres
	log.Println("Running Auto Migration...")
	db.AutoMigrate(&models.Material{})

	// 3. Jalankan Seeder (Refresh data)
	SeedMaterials(db)

	// 4. Dependency Injection
	repo := repository.NewMaterialRepository(db)
	uc := usecase.NewMaterialUsecase(repo)
	handler := http.NewMaterialHandler(uc)

	// 5. Setup Router
	// 5. Setup Router
    r := gin.Default()
    r.Use(CORSMiddleware())

    r.GET("/", func(c *gin.Context) {
        c.JSON(200, gin.H{"status": "Materi Service is Running with Postgres"})
    })

    // 🔥 GRUP UTAMA: Menangani semua variasi tembakan Flutter & Laravel
    materiRoute := r.Group("/api/materials")
    {
        materiRoute.GET("", handler.GetMaterials)            // Menangani: GET    /api/materials (Untuk Flutter Lokal/AWS)
        materiRoute.POST("", handler.SyncMaterial)           // Menangani: POST   /api/materials
        materiRoute.POST("/sync", handler.SyncMaterial)      // Menangani: POST   /api/materials/sync (Untuk Laravel)
        materiRoute.PUT("/:id", handler.UpdateMaterial)      // Menangani: PUT    /api/materials/:id
        materiRoute.DELETE("/:id", handler.DeleteMaterial)   // Menangani: DELETE /api/materials/:id
    }

	port := os.Getenv("PORT")
	if port == "" {
		port = "9001"
	}
	r.Run(":" + port)
}