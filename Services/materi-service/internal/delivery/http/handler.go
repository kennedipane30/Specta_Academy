package http

import (
	"materi-service/internal/models"
	"materi-service/internal/usecase"
	"net/http"

	"github.com/gin-gonic/gin"
)

type MaterialHandler struct {
	uc usecase.MaterialUsecase
}

func NewMaterialHandler(uc usecase.MaterialUsecase) *MaterialHandler {
	return &MaterialHandler{uc}
}

func (h *MaterialHandler) SyncMaterial(c *gin.Context) {
	var m models.Material
	if err := c.ShouldBindJSON(&m); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.SyncMaterial(&m); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Material synced successfully"})
}

func (h *MaterialHandler) GetMaterials(c *gin.Context) {
	classID := c.Query("class_id")
	materials, err := h.uc.GetMaterials(classID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"data": materials})
}

// ✨ HANDLER UPDATE
func (h *MaterialHandler) UpdateMaterial(c *gin.Context) {
	id := c.Param("id")
	var m models.Material
	if err := c.ShouldBindJSON(&m); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": err.Error()})
		return
	}
	if err := h.uc.UpdateMaterial(id, &m); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Material updated successfully"})
}

// ✨ HANDLER DELETE
func (h *MaterialHandler) DeleteMaterial(c *gin.Context) {
	id := c.Param("id")
	if err := h.uc.DeleteMaterial(id); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": err.Error()})
		return
	}
	c.JSON(http.StatusOK, gin.H{"message": "Material deleted successfully"})
}