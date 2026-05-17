package repository

import (
	"materi-service/internal/models"
	"gorm.io/gorm"
)

type MaterialRepository interface {
	SyncMaterial(materi models.Material) error
	GetByClass(classID uint) ([]models.Material, error) // ✨ Tambahkan ini
	GetByClassAndSubject(classID uint, subjectName string) ([]models.Material, error)
}

type materialRepo struct {
	db *gorm.DB
}

func NewMaterialRepository(db *gorm.DB) MaterialRepository {
	return &materialRepo{db}
}

func (r *materialRepo) SyncMaterial(materi models.Material) error {
	return r.db.Save(&materi).Error
}

func (r *materialRepo) GetByClass(classID uint) ([]models.Material, error) {
	var materials []models.Material
	err := r.db.Where("class_id = ?", classID).Order("week asc").Find(&materials).Error
	return materials, err
}

func (r *materialRepo) GetByClassAndSubject(classID uint, subjectName string) ([]models.Material, error) {
	var materials []models.Material
	err := r.db.Where("class_id = ? AND material_name = ?", classID, subjectName).
		Order("week asc").Find(&materials).Error
	return materials, err
}