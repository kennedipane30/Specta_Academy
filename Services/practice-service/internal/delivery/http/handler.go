package http

import (
	"log"
	"net/http"
	"practice-service/internal/models"
	"practice-service/internal/usecase"
	"strconv"

	"github.com/gin-gonic/gin"
)

type PracticeHandler struct {
	uc usecase.PracticeUsecase
}

func NewPracticeHandler(uc usecase.PracticeUsecase) *PracticeHandler {
	return &PracticeHandler{uc}
}

// GetPractice retrieves practice questions by class_id
func (h *PracticeHandler) GetPractice(c *gin.Context) {
	classIDStr := c.Query("class_id")
	if classIDStr == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "class_id is required"})
		return
	}

	classID, err := strconv.Atoi(classIDStr)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "invalid class_id"})
		return
	}

	data, err := h.uc.GetListByClass(uint(classID))
	if err != nil {
		log.Printf("Error getting practice questions: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Internal Server Error"})
		return
	}

	c.JSON(http.StatusOK, data)
}

// Sync handles bulk import of practice questions from CSV
func (h *PracticeHandler) Sync(c *gin.Context) {
	var req []models.PracticeQuestion
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}

	if len(req) == 0 {
		c.JSON(http.StatusBadRequest, gin.H{"error": "no data to sync"})
		return
	}

	if err := h.uc.SyncQuestions(req); err != nil {
		log.Printf("Error syncing questions: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Sync Success", "total": len(req)})
}

// DeleteWeek deletes all practice questions for a specific class, subject, and week
// Supports subject from:
// - Query string: ?subject=MATHEMATICS
// - JSON body: {"subject": "MATHEMATICS"}
// - Form data: subject=MATHEMATICS
func (h *PracticeHandler) DeleteWeek(c *gin.Context) {
	// Parse and validate class_id
	classID, err := strconv.Atoi(c.Param("class_id"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "invalid class_id"})
		return
	}

	// Parse and validate week
	week, err := strconv.Atoi(c.Param("week"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "invalid week"})
		return
	}

	// Get subject from multiple sources
	subject := h.extractSubject(c)

	// Log request for debugging
	log.Printf("[DELETE] Request: class_id=%d, week=%d, subject='%s'", classID, week, subject)

	// Validate subject is provided
	if subject == "" {
		c.JSON(http.StatusBadRequest, gin.H{
			"error":   "subject is required",
			"message": "Please provide subject parameter in query string, JSON body, or form data",
		})
		return
	}

	// Execute deletion
	if err := h.uc.RemoveWeek(uint(classID), subject, week); err != nil {
		log.Printf("[DELETE] Error: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete: " + err.Error()})
		return
	}

	log.Printf("[DELETE] Success: class_id=%d, week=%d, subject='%s'", classID, week, subject)
	c.JSON(http.StatusOK, gin.H{
		"message":  "Deleted successfully",
		"class_id": classID,
		"week":     week,
		"subject":  subject,
	})
}

// extractSubject extracts subject from various request sources
func (h *PracticeHandler) extractSubject(c *gin.Context) string {
	// Priority 1: Query string
	if subject := c.Query("subject"); subject != "" {
		return subject
	}

	// Priority 2: JSON body
	var body map[string]interface{}
	if err := c.ShouldBindJSON(&body); err == nil {
		if subject, ok := body["subject"].(string); ok && subject != "" {
			return subject
		}
	}

	// Priority 3: Form data (x-www-form-urlencoded)
	if subject := c.PostForm("subject"); subject != "" {
		return subject
	}

	// Priority 4: Raw body as string (for simple text/plain requests)
	if c.Request.Body != nil {
		var rawBody map[string]string
		if err := c.ShouldBindJSON(&rawBody); err == nil {
			if subject, ok := rawBody["subject"]; ok && subject != "" {
				return subject
			}
		}
	}

	return ""
}