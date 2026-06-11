package http

import (
	"encoding/json" 
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

type SyncRequest struct {
	Tryout    models.Tryout     `json:"tryout"`
	Questions []models.Question `json:"questions"`
}

func (h *TryoutHandler) SyncTryout(c *gin.Context) {
	var req SyncRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format JSON salah"})
		return
	}

	if err := h.uc.SyncTryout(req.Tryout, req.Questions); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal simpan ke database"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Paket Tryout Berhasil Masuk ke Go"})
}

func (h *TryoutHandler) SyncSubmissions(c *gin.Context) {
	var s models.TryoutSubmission
	if err := c.ShouldBindJSON(&s); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format data submission salah"})
		return
	}

	if err := h.uc.SyncSubmissions(s); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal simpan riwayat di Go"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Riwayat berhasil disimpan di Go"})
}

func (h *TryoutHandler) GetTryouts(c *gin.Context) {
	classID := c.Query("class_id")
	userID := c.Query("user_id")

	if userID == "" {
		userID = "0"
	}

	data, err := h.uc.GetTryouts(classID, userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": "Gagal mengambil daftar tryout"})
		return
	}
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": data})
}

func (h *TryoutHandler) GetQuestions(c *gin.Context) {
	tryoutID := c.Param("id")
	if tryoutID == "" {
		tryoutID = c.Query("tryout_id")
	}

	if tryoutID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "tryout_id is required"})
		return
	}

	data, err := h.uc.GetQuestions(tryoutID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database Error"})
		return
	}

	c.JSON(http.StatusOK, data)
}

func (h *TryoutHandler) SubmitTryout(c *gin.Context) {
	var req struct {
		TryoutID int               `json:"tryout_id"`
		UserID   int               `json:"user_id"`
		Answers  map[string]string `json:"answers"`
	}

	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format data jawaban salah"})
		return
	}

	tryoutIDStr := strconv.Itoa(req.TryoutID)
	score, correctCount, err := h.uc.CalculateScore(tryoutIDStr, req.Answers)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghitung nilai ujian"})
		return
	}

	answersJSONBytes, _ := json.Marshal(req.Answers)

	submission := models.TryoutSubmission{
		TryoutID: uint(req.TryoutID),
		UserID:   uint(req.UserID),
		Score:    float64(score),
		Answers:  string(answersJSONBytes), 
	}
	h.uc.SyncSubmissions(submission)

	c.JSON(http.StatusOK, gin.H{
		"status":  "success",
		"score":   score,
		"correct": correctCount,
		"message": "Nilai berhasil disimpan di database",
	})
}

// ✨ FUNGSI BARU: Untuk mengambil riwayat Tryout bagi Report Page
func (h *TryoutHandler) GetHistory(c *gin.Context) {
	userID := c.Query("user_id")
	if userID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "user_id is required"})
		return
	}

	data, err := h.uc.GetHistory(userID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil riwayat"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "data": data})
}

// ✅ TAMBAH: GetSubmissions untuk admin melihat submissions per tryout
func (h *TryoutHandler) GetSubmissions(c *gin.Context) {
	tryoutID := c.Query("tryout_id")
	if tryoutID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "tryout_id is required"})
		return
	}

	data, err := h.uc.GetSubmissionsByTryout(tryoutID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil submissions: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "data": data})
}

// Tambahkan method ini di akhir file handler.go

// DeleteTryout - Menghapus paket tryout beserta questions dan submissions
func (h *TryoutHandler) DeleteTryout(c *gin.Context) {
	tryoutID := c.Param("id")
	if tryoutID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "tryout_id is required"})
		return
	}

	if err := h.uc.DeleteTryout(tryoutID); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghapus paket: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  "success",
		"message": "Paket tryout berhasil dihapus",
	})
}