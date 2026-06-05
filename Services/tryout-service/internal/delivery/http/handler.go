package http

import (
	"net/http" // ✨ Sekarang digunakan untuk http.StatusOK, dll
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

// Struct untuk bungkusan data dari Laravel
type SyncRequest struct {
	Tryout    models.Tryout     `json:"tryout"`
	Questions []models.Question `json:"questions"`
}

/**
 * 1. SINKRONISASI PAKET SOAL
 */
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

/**
 * 2. SINKRONISASI HASIL/NILAI SISWA
 */
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

/**
 * 3. AMBIL DAFTAR TRYOUT
 */
func (h *TryoutHandler) GetTryouts(c *gin.Context) {
	classID := c.Query("class_id")
	data, err := h.uc.GetTryouts(classID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "message": "Gagal mengambil daftar tryout"})
		return
	}
	c.JSON(http.StatusOK, gin.H{"status": "success", "data": data})
}

/**
 * 4. AMBIL DAFTAR SOAL (SAAT UJIAN DIMULAI)
 */
func (h *TryoutHandler) GetQuestions(c *gin.Context) {
	// Mendukung ambil ID dari path (:id) atau query (?tryout_id=)
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

	// Mengembalikan list soal langsung agar Flutter bisa membaca sebagai List
	c.JSON(http.StatusOK, data)
}