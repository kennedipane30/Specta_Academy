package repository

import (
	"materi-service/internal/models"
	"gorm.io/gorm"
)

type MaterialRepository interface {
	Save(material *models.Material) error
	GetAll(classID string) ([]models.Material, error)
	GetByID(id string) (models.Material, error)
	Update(id string, material *models.Material) error
	Delete(id string) error
}

type materialRepository struct {
	db *gorm.DB
}

func NewMaterialRepository(db *gorm.DB) MaterialRepository {
	return &materialRepository{db}
}

func (r *materialRepository) Save(material *models.Material) error {
	return r.db.Create(material).Error
}

// MODIFIKASI DISINI: Menambahkan logika sorting class_id dan material_id
func (r *materialRepository) GetAll(classID string) ([]models.Material, error) {
	var materials []models.Material
	
	query := r.db.Order("class_id ASC, material_id ASC")

	if classID != "" {
		query = query.Where("class_id = ?", classID)
	}

	err := query.Find(&materials).Error
	return materials, err
}

func (r *materialRepository) GetByID(id string) (models.Material, error) {
	var m models.Material
	err := r.db.Where("material_id = ?", id).First(&m).Error
	return m, err
}

func (r *materialRepository) Update(id string, m *models.Material) error {
	return r.db.Model(&models.Material{}).Where("material_id = ?", id).Updates(m).Error
}

func (r *materialRepository) Delete(id string) error {
	return r.db.Where("material_id = ?", id).Delete(&models.Material{}).Error
}