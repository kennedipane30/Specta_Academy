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

	// 5. API Group (Menyesuaikan GO_MATERI_URL=.../api di .env Laravel)
	api := r.Group("/api")
	{
		api.POST("/materials/sync", handler.SyncMaterial)
		api.GET("/materials", handler.GetMaterials)
	}

	// 6. Ambil Port dari .env (Sesuai Laravel: 9001)
	port := os.Getenv("PORT")
	if port == "" { 
		port = "9001" 
	}
	
	r.Run(":" + port)
}