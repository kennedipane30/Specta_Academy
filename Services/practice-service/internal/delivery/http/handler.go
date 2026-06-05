package http

import (
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

func (h *PracticeHandler) GetPractice(c *gin.Context) {
	classIDStr := c.Query("class_id")
	if classIDStr == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "class_id is required"})
		return
	}

	classID, _ := strconv.Atoi(classIDStr)
	
	data, err := h.uc.GetListByClass(uint(classID))
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Internal Server Error"})
		return
	}

	// ✨ MODIFIKASI: Kirim data langsung sebagai array [...]
	// Agar di Flutter: pracDecoded is List bernilai TRUE
	c.JSON(http.StatusOK, data)
}

func (h *PracticeHandler) Sync(c *gin.Context) {
	var req []models.PracticeQuestion
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.SyncQuestions(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Sync Success"})
}

func (h *PracticeHandler) DeleteWeek(c *gin.Context) {
	classID, _ := strconv.Atoi(c.Param("class_id"))
	week, _ := strconv.Atoi(c.Param("week"))
	subject := c.Query("subject")

	if err := h.uc.RemoveWeek(uint(classID), subject, week); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Deleted successfully"})
}