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

// ==================== DRAFT HANDLERS ====================

// CreateDraft - Membuat draft soal baru
func (h *TryoutHandler) CreateDraft(c *gin.Context) {
	var req models.DraftRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format JSON salah: " + err.Error()})
		return
	}

	if err := h.uc.CreateDraft(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menyimpan draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Draft soal berhasil disimpan"})
}

// UpdateDraft - Mengupdate draft soal
func (h *TryoutHandler) UpdateDraft(c *gin.Context) {
	var req models.DraftRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Format JSON salah: " + err.Error()})
		return
	}

	id := c.Param("id")
	if id == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "id is required"})
		return
	}

	idUint, err := strconv.ParseUint(id, 10, 32)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid id format"})
		return
	}
	req.ID = uint(idUint)

	if err := h.uc.UpdateDraft(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal update draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Draft soal berhasil diupdate"})
}

// DeleteDraft - Menghapus draft soal
func (h *TryoutHandler) DeleteDraft(c *gin.Context) {
	draftID := c.Param("id")
	if draftID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "id is required"})
		return
	}

	if err := h.uc.DeleteDraft(draftID); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghapus draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Draft soal berhasil dihapus"})
}

// DeleteAllDrafts - Menghapus semua draft (bisa berdasarkan class_id atau user_id)
func (h *TryoutHandler) DeleteAllDrafts(c *gin.Context) {
	classID := c.Query("class_id")
	subjectName := c.Query("subject_name")
	userIDStr := c.Query("user_id")

	if classID == "" && userIDStr == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "class_id or user_id is required"})
		return
	}

	var userID uint = 0
	if userIDStr != "" {
		parsed, _ := strconv.ParseUint(userIDStr, 10, 32)
		userID = uint(parsed)
	}

	if err := h.uc.DeleteAllDrafts(classID, userID, subjectName); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghapus draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "message": "Semua draft berhasil dihapus"})
}

// GetDrafts - Mendapatkan daftar draft (bisa dengan atau tanpa filter)
func (h *TryoutHandler) GetDrafts(c *gin.Context) {
	classID := c.Query("class_id")
	subjectName := c.Query("subject_name")
	userIDStr := c.Query("user_id")

	var drafts []models.TryoutDraft
	var err error

	if classID == "" && userIDStr == "" {
		drafts, err = h.uc.GetAllDrafts()
	} else if classID != "" && subjectName != "" && userIDStr != "" {
		var userID uint = 0
		if userIDStr != "" {
			parsed, _ := strconv.ParseUint(userIDStr, 10, 32)
			userID = uint(parsed)
		}
		drafts, err = h.uc.GetDraftsByClassAndSubject(classID, userID, subjectName)
	} else if classID != "" {
		drafts, err = h.uc.GetDraftsByClass(classID)
	} else {
		drafts, err = h.uc.GetDraftsByUser(userIDStr)
	}

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "data": drafts})
}

// GetDraftByID - Mendapatkan detail draft berdasarkan ID
func (h *TryoutHandler) GetDraftByID(c *gin.Context) {
	draftID := c.Param("id")
	if draftID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "id is required"})
		return
	}

	draft, err := h.uc.GetDraftByID(draftID)
	if err != nil {
		c.JSON(http.StatusNotFound, gin.H{"error": "Draft tidak ditemukan"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "data": draft})
}

// GetDraftCount - Mendapatkan jumlah draft (bisa berdasarkan class_id atau user_id)
func (h *TryoutHandler) GetDraftCount(c *gin.Context) {
	classID := c.Query("class_id")
	subjectName := c.Query("subject_name")
	userIDStr := c.Query("user_id")

	// Jika hanya user_id yang diberikan
	if classID == "" && userIDStr != "" {
		userID, _ := strconv.ParseUint(userIDStr, 10, 32)
		count, err := h.uc.GetDraftCountByUser(uint(userID))
		if err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil jumlah draft: " + err.Error()})
			return
		}
		c.JSON(http.StatusOK, gin.H{"status": "success", "count": count})
		return
	}

	if classID == "" {
		c.JSON(http.StatusBadRequest, gin.H{"error": "class_id is required"})
		return
	}

	var userID uint = 0
	if userIDStr != "" {
		parsed, _ := strconv.ParseUint(userIDStr, 10, 32)
		userID = uint(parsed)
	}

	count, err := h.uc.GetDraftCount(classID, userID, subjectName)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil jumlah draft: " + err.Error()})
		return
	}

	c.JSON(http.StatusOK, gin.H{"status": "success", "count": count})
}