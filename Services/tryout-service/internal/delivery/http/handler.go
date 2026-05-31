package http

import (
	"fmt"
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

// Struct untuk membongkar bungkusan JSON dari Laravel (Header TO + Daftar Soal)
type SyncRequest struct {
	Tryout    models.Tryout     `json:"tryout"`
	Questions []models.Question `json:"questions"`
}

/**
 * 1. SINKRONISASI PAKET SOAL (DARI ADMIN LARAVEL)
 */
func (h *TryoutHandler) SyncTryout(c *gin.Context) {
	var req SyncRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		fmt.Println("❌ BIND ERROR (SyncTryout):", err.Error())
		c.JSON(400, gin.H{"error": "Format JSON salah"})
		return
	}

	if err := h.uc.SyncTryout(req.Tryout, req.Questions); err != nil {
		fmt.Println("❌ USECASE ERROR:", err.Error())
		c.JSON(500, gin.H{"error": "Gagal simpan ke database"})
		return
	}

	c.JSON(200, gin.H{"status": "success", "message": "Paket Tryout Berhasil Masuk ke Go"})
}

/**
 * 2. SINKRONISASI HASIL/NILAI SISWA (DARI LARAVEL -> GO)
 * ✨ PERBAIKAN: Sekarang benar-benar memanggil h.uc.SyncSubmissions
 */
func (h *TryoutHandler) SyncSubmissions(c *gin.Context) {
	var s models.TryoutSubmission
	if err := c.ShouldBindJSON(&s); err != nil {
		fmt.Println("❌ BIND ERROR (SyncSubmissions):", err.Error())
		c.JSON(400, gin.H{"error": "Format data submission salah"})
		return
	}

	fmt.Printf("📥 Mencatat Nilai Siswa ID %d untuk TO ID %d ke Database Go\n", s.UserID, s.TryoutID)

	if err := h.uc.SyncSubmissions(s); err != nil {
		fmt.Println("❌ DATABASE SUBMISSION ERROR:", err.Error())
		c.JSON(500, gin.H{"error": "Gagal simpan riwayat di Go"})
		return
	}

	c.JSON(200, gin.H{"status": "success", "message": "Riwayat berhasil disimpan di Go"})
}

/**
 * 3. AMBIL DAFTAR TRYOUT (UNTUK HP SISWA)
 */
func (h *TryoutHandler) GetTryouts(c *gin.Context) {
	classID := c.Query("class_id")
	data, err := h.uc.GetTryouts(classID)
	if err != nil {
		c.JSON(500, gin.H{"status": "error", "message": err.Error()})
		return
	}
	c.JSON(200, gin.H{"status": "success", "data": data})
}

/**
 * 4. AMBIL DAFTAR SOAL (SAAT UJIAN DIMULAI)
 */
func (h *TryoutHandler) GetQuestions(c *gin.Context) {
	tryoutID := c.Query("tryout_id")
	if tryoutID == "" {
		c.JSON(400, gin.H{"error": "tryout_id is required"})
		return
	}

	data, err := h.uc.GetQuestions(tryoutID)
	if err != nil {
		c.JSON(500, gin.H{"error": "Database Error"})
		return
	}
	c.JSON(200, gin.H{"status": "success", "data": data})
}