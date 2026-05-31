package main

import (
	"materi-service/config"
	"materi-service/internal/delivery/http"
	"materi-service/internal/models"
	"materi-service/internal/repository"
	"materi-service/internal/usecase"
	"os"

	"github.com/gin-gonic/gin"
)

// Middleware untuk mengizinkan akses dari service lain (CORS)
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

func main() {
	// 1. Inisialisasi Database
	db := config.InitDB()
	
	// 2. Jalankan migrasi agar tabel materials otomatis terbuat di Postgres
	db.AutoMigrate(&models.Material{})

	// 3. Dependency Injection
	repo := repository.NewMaterialRepository(db)
	uc := usecase.NewMaterialUsecase(repo)
	handler := http.NewMaterialHandler(uc)

	// 4. Setup Router
	r := gin.Default()

	// Gunakan Middleware
	r.Use(CORSMiddleware())

	// Route cek kesehatan server
	r.GET("/", func(c *gin.Context) {
		c.JSON(200, gin.H{"status": "Materi Service is Running"})
	})

	// 5. API Group (Menyesuaikan pemanggilan Laravel)
	api := r.Group("/api")
	{
		// --- CREATE / SYNC ---
		// Baris ini krusial untuk memperbaiki Error 404 pada Laravel
		api.POST("/materials", handler.SyncMaterial) 
		api.POST("/materials/sync", handler.SyncMaterial) // Alias untuk cadangan

		// --- READ ---
		api.GET("/materials", handler.GetMaterials)

		// --- UPDATE ---
		// Digunakan untuk mengubah judul materi atau data lainnya
		api.PUT("/materials/:id", handler.UpdateMaterial)

		// --- DELETE ---
		// Digunakan untuk menghapus materi (Ikon Sampah di Web Pengajar)
		api.DELETE("/materials/:id", handler.DeleteMaterial)
	}

	// 6. Ambil Port dari .env (Default: 9001)
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9001" 
	}
	
	// Jalankan Server
	r.Run(":" + port)
}