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

	// Auto migrate tables
	db.AutoMigrate(
		&models.Tryout{},
		&models.Question{},
		&models.TryoutSubmission{},
		&models.TryoutDraft{},
	)

	repo := repository.NewTryoutRepository(db)
	uc := usecase.NewTryoutUsecase(repo)
	handler := http.NewTryoutHandler(uc)

	r := gin.Default()
	r.Use(CORSMiddleware())

	r.GET("/", func(c *gin.Context) {
		c.JSON(200, gin.H{"message": "Tryout Service is Running on Port 9002"})
	})

	// ============================================================
	// 🌐 SINKRONISASI ROUTING UNTUK TRYOUT SERVICE
	// ============================================================

	// 1. Grup Standar (/api) - Digunakan untuk sub-routing spesifik
	api := r.Group("/api")
	{
		api.GET("/questions", handler.GetQuestions)
		api.POST("/tryouts/sync", handler.SyncTryout)
		api.POST("/tryouts/submissions/sync", handler.SyncSubmissions)

		// Drafts Management (Digunakan oleh Web Guru / Pengajar)
		api.POST("/tryouts/drafts", handler.CreateDraft)
		api.PUT("/tryouts/drafts/:id", handler.UpdateDraft)
		api.DELETE("/tryouts/drafts/:id", handler.DeleteDraft)
		api.DELETE("/tryouts/drafts", handler.DeleteAllDrafts)
		api.GET("/tryouts/drafts", handler.GetDrafts)
		api.GET("/tryouts/drafts/:id", handler.GetDraftByID)
		api.GET("/tryouts/drafts/count", handler.GetDraftCount)
	}

	// 2. 🔥 GRUP UTAMA (/api/tryouts) - Kebal Duplikasi & Mendukung Semua Client
	tryoutsGroup := r.Group("/api/tryouts")
	{
		tryoutsGroup.GET("", handler.GetTryouts)                 // GET    /api/tryouts (Flutter & Web Guru)
		tryoutsGroup.DELETE("/:id", handler.DeleteTryout)         // DELETE /api/tryouts/:id
		tryoutsGroup.GET("/:id/questions", handler.GetQuestions) // GET    /api/tryouts/:id/questions
		tryoutsGroup.POST("/:id/submit", handler.SubmitTryout)   // POST   /api/tryouts/:id/submit
		tryoutsGroup.GET("/submissions", handler.GetSubmissions) // GET    /api/tryouts/submissions
		tryoutsGroup.GET("/history", handler.GetHistory)         // GET    /api/tryouts/history
	}

	// Determine Port
	port := os.Getenv("PORT")
	if port == "" {
		port = "9002"
	}

	fmt.Println("🚀 Spekta Tryout Service successfully started on port: " + port)

	err := r.Run(":" + port)
	if err != nil {
		fmt.Printf("❌ Fatal: Gagal menjalankan server: %v\n", err)
	}
}