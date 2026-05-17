package http

import (
	"net/http"
	"strconv"
	"tryout-service/internal/models"
	"tryout-service/internal/usecase"
	"github.com/gin-gonic/gin"
)

type TryoutHandler struct {
	uc usecase.TryoutUsecase 
}

func NewTryoutHandler(uc usecase.TryoutUsecase) *TryoutHandler { 
	return &TryoutHandler{uc}
}

func (h *TryoutHandler) SyncTryout(c *gin.Context) {
	var payload struct {
		Tryout    models.Tryout     `json:"tryout"`
		Questions []models.Question `json:"questions"`
	}
	if err := c.ShouldBindJSON(&payload); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.ProcessSync(payload.Tryout, payload.Questions); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Tryout and Questions synced successfully"})
}

func (h *TryoutHandler) SyncSubmissions(c *gin.Context) {
	var req []models.TryoutSubmission
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.ProcessSubmissionsSync(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Submissions synced successfully"})
}

// ✨ Handler Baru: Ambil Daftar Tryout
func (h *TryoutHandler) GetTryouts(c *gin.Context) {
	classID, _ := strconv.Atoi(c.Query("class_id"))
	data, err := h.uc.FetchTryoutsByClass(uint(classID))
	if err != nil {
		c.JSON(500, gin.H{"status": "error", "data": []interface{}{}})
		return
	}
	c.JSON(200, gin.H{"status": "success", "data": data})
}

// ✨ Handler Baru: Ambil Detail Soal Tryout
func (h *TryoutHandler) GetQuestions(c *gin.Context) {
	tryoutID, _ := strconv.Atoi(c.Query("tryout_id"))
	data, err := h.uc.FetchQuestionsByTryout(uint(tryoutID))
	if err != nil {
		c.JSON(500, gin.H{"status": "error", "data": []interface{}{}})
		return
	}
	c.JSON(200, gin.H{"status": "success", "data": data})
}