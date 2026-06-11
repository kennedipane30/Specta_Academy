package main

import (
	"log"
	"os"
	"practice-service/config"
	"practice-service/internal/delivery/http"
	"practice-service/internal/models"
	"practice-service/internal/repository"
	"practice-service/internal/usecase"

	"github.com/gin-gonic/gin"
)

// CORSMiddleware handles CORS headers for cross-origin requests
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
	// Initialize Database
	db := config.InitDB()

	// Auto Migrate Tables
	if err := db.AutoMigrate(&models.PracticeQuestion{}); err != nil {
		log.Fatalf("Failed to migrate database: %v", err)
	}
	log.Println("Database migration completed successfully")

	// Dependency Injection
	repo := repository.NewPracticeRepository(db)
	uc := usecase.NewPracticeUsecase(repo)
	handler := http.NewPracticeHandler(uc)

	// Initialize Gin
	r := gin.Default()
	r.Use(CORSMiddleware())

	// API Routes
	api := r.Group("/api")
	{
		// GET Routes
		api.GET("/tryouts", handler.GetPractice)
		api.GET("/practice", handler.GetPractice)

		// POST Routes
		api.POST("/practice/sync", handler.Sync)

		// DELETE Routes
		api.DELETE("/practice/class/:class_id/week/:week", handler.DeleteWeek)
		
		// Support POST with _method=DELETE for Laravel compatibility
		api.POST("/practice/class/:class_id/week/:week", handler.DeleteWeek)
	}

	// Determine Port
	port := os.Getenv("PORT")
	if port == "" {
		port = "9003"
	}

	// Start Server
	log.Printf("Practice Service is starting on port %s", port)
	log.Printf("Available endpoints:")
	log.Printf("  GET    /api/tryouts?class_id={id}")
	log.Printf("  GET    /api/practice?class_id={id}")
	log.Printf("  POST   /api/practice/sync")
	log.Printf("  DELETE /api/practice/class/{id}/week/{week}?subject={subject}")
	log.Printf("  POST   /api/practice/class/{id}/week/{week} (with _method=DELETE)")

	if err := r.Run(":" + port); err != nil {
		log.Fatal("Failed to start server: ", err)
	}
}