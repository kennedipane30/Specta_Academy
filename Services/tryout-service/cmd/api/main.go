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
	db := config.InitDB()

	// HANYA MIGRATE TABEL YANG DIPERLUKAN
	db.AutoMigrate(
		&models.Tryout{},
		&models.Question{},
		&models.TryoutSubmission{}, 
	)

	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	r := gin.Default()
	r.Use(CORSMiddleware())

	r.GET("/", func(c *gin.Context) {
		c.JSON(200, gin.H{"message": "Tryout Service is Running on Port 9002"})
	})

	api := r.Group("/api")
	{
		api.POST("/tryouts/sync", handler.SyncTryout)
		api.POST("/tryouts/submissions/sync", handler.SyncSubmissions)
		api.GET("/tryouts", handler.GetTryouts)
		api.GET("/tryouts/:id/questions", handler.GetQuestions)
		api.POST("/tryouts/:id/submit", handler.SubmitTryout)
		api.GET("/questions", handler.GetQuestions)
		
		// ✨ MODIFIKASI: Menambahkan rute Endpoint untuk mengambil riwayat Tryout
		api.GET("/tryouts/history", handler.GetHistory)
	}

	port := os.Getenv("PORT")
	if port == "" {
		port = "9002"
	}

	fmt.Println("🚀 Spekta Tryout Service started on port: " + port)

	err := r.Run(":" + port)
	if err != nil {
		fmt.Printf("❌ Fatal: Gagal menjalankan server: %v\n", err)
	}
}