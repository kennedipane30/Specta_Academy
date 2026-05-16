package http

import (
	"materi-service/internal/models"
	"materi-service/internal/usecase"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type MaterialHandler struct {
	uc usecase.MaterialUsecase
}

func NewMaterialHandler(uc usecase.MaterialUsecase) *MaterialHandler {
	return &MaterialHandler{uc}
}

func (h *MaterialHandler) SyncMaterial(c *gin.Context) {
	var req models.Material
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"message": err.Error()})
		return
	}
	if err := h.uc.Sync(req); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"message": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Sync Success"})
}

func (h *MaterialHandler) GetMaterials(c *gin.Context) {
	classIDStr := c.Query("class_id")
	subjectName := c.Query("subject_name")
	
	if classIDStr == "" {
		c.JSON(http.StatusBadRequest, gin.H{"status": "error", "message": "class_id is required"})
		return
	}

	classID, _ := strconv.Atoi(classIDStr)
	var data []models.Material
	var err error

	if subjectName != "" {
		// Jika Flutter memilih salah satu Mata Pelajaran (Detail per minggu)
		data, err = h.uc.FetchMaterialsBySubject(uint(classID), subjectName)
	} else {
		// ✨ Jika Laravel Gateway meminta daftar awal semua materi di kelas
		data, err = h.uc.FetchMaterialsByClass(uint(classID))
	}

	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"status": "error", "data": []interface{}{}})
		return
	}

	c.JSON(http.StatusOK, gin.H{
		"status": "success",
		"data":   data,
	})
}