package main

import (
	"tryout-service/config"
	"tryout-service/internal/delivery/http"
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
	"tryout-service/internal/usecase"
	"os"
	"github.com/gin-gonic/gin"
)

func main() {
	db := config.InitDB()
	
	// Auto Migrate (Memastikan tabel sinkron dengan Laravel)
	db.AutoMigrate(&models.Tryout{}, &models.Question{}, &models.TryoutResult{}, &models.TryoutSubmission{})

	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	r := gin.Default()

	api := r.Group("/api")
	{
		// Sisi Admin & Pengajar
		api.POST("/tryouts/sync", handler.SyncTryout)
		api.POST("/tryouts/submissions/sync", handler.SyncSubmissions)

		// Sisi Siswa (Akses dari Flutter via Gateway Laravel)
		api.GET("/tryouts", handler.GetTryouts)
		api.GET("/questions", handler.GetQuestions)
	}

	port := os.Getenv("PORT")
	if port == "" { port = "9002" }
	r.Run(":" + port)
}