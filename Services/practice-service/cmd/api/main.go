package main

import (
	"os"
	"practice-service/config"
	"practice-service/internal/delivery/http"
	"practice-service/internal/models"
	"practice-service/internal/repository"
	"practice-service/internal/usecase"

	"github.com/gin-gonic/gin"
)

func main() {
	// 1. Inisialisasi Database PostgreSQL
	db := config.InitDB()
	
	// 2. Migrasi tabel otomatis (Membuat tabel jika belum ada)
	db.AutoMigrate(&models.PracticeQuestion{}) 

	// 3. Dependency Injection (Menghubungkan Layer)
	repo := repository.NewPracticeRepository(db)
	uc := usecase.NewPracticeUsecase(repo)
	handler := http.NewPracticeHandler(uc)

	// 4. Setup Router menggunakan Gin
	r := gin.Default()

	// 5. API Group (Menyesuaikan GO_PRACTICE_URL=.../api di .env Laravel)
	api := r.Group("/api")
	{
		// Endpoint untuk Sinkronisasi data dari Laravel (Admin/Pengajar)
		api.POST("/practice/sync", handler.Sync)
		
		// ✨ ENDPOINT BARU: Untuk mengambil data (Daftar Mata Pelajaran & Soal)
		// URL: http://localhost:9003/api/practice?class_id=1
		api.GET("/practice", handler.GetPractice) 

		// Endpoint untuk menghapus data per minggu
		api.DELETE("/practice/:class_id/:week", handler.DeleteWeek)
	}

	// 6. Ambil Port dari .env (Sesuai Laravel: 9003)
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9003" 
	}
	
	// 7. Jalankan Server
	r.Run(":" + port)
}