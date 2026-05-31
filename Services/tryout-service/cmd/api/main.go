package main

import (
	"fmt"
	"os"
	"tryout-service/config"
	"tryout-service/internal/delivery/http"
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
	"tryout-service/internal/usecase"

	"github.com/gin-gonic/gin"
)

// CORSMiddleware untuk memberikan akses kepada Laravel Gateway & Flutter
func CORSMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		c.Writer.Header().Set("Access-Control-Allow-Origin", "*")
		c.Writer.Header().Set("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE")
		c.Writer.Header().Set("Access-Control-Allow-Headers", "Content-Type, Content-Length, Authorization, Accept")
		
		if c.Request.Method == "OPTIONS" {
			c.AbortWithStatus(204)
			return
		}
		c.Next()
	}
}

func main() {
	// 1. Inisialisasi Database (specta_tryout)
	db := config.InitDB()
	
	// 2. Jalankan Migrasi Tabel Otomatis
	db.AutoMigrate(
		&models.Tryout{}, 
		&models.Question{}, 
		&models.TryoutResult{}, 
		&models.TryoutSubmission{},
	)

	// 3. Dependency Injection (Layer Architecture)
	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	// 4. Setup Router Gin
	r := gin.Default()
	r.Use(CORSMiddleware())

	// Route Cek Status Utama
	r.GET("/", func(c *gin.Context) {
		c.JSON(200, gin.H{"message": "Tryout Service is Running"})
	})

	// 5. API Group - Sinkron dengan Gateway Laravel
	api := r.Group("/api")
	{
		// --- ENDPOINT ADMIN & PENGAJAR ---
		api.POST("/tryouts/sync", handler.SyncTryout) 
		api.POST("/tryouts/submissions/sync", handler.SyncSubmissions)

		// --- ENDPOINT SISWA (DIPANGGIL MOBILE VIA LARAVEL) ---
		api.GET("/tryouts", handler.GetTryouts)
		
		// Mendukung dua versi route agar tidak terjadi 404
		api.GET("/questions", handler.GetQuestions)
		api.GET("/tryouts/questions", handler.GetQuestions) // Alias baru
	}

	// 6. Konfigurasi Port (Membaca .env atau Default ke 9003)
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9003" 
	}
	
	fmt.Println("🚀 Spekta Tryout Service started on port: " + port)
	
	// Jalankan Server
	err := r.Run(":" + port)
	if err != nil {
		fmt.Printf("❌ Fatal: Gagal menjalankan server: %v\n", err)
	}
}