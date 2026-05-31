package repository

import (
	"materi-service/internal/models"
	"gorm.io/gorm"
)

// MaterialRepository mendefinisikan kontrak fungsi untuk berinteraksi dengan database
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

// NewMaterialRepository membuat instance baru dari materialRepository
func NewMaterialRepository(db *gorm.DB) MaterialRepository {
	return &materialRepository{db}
}

// Save menyimpan data materi baru ke database
func (r *materialRepository) Save(material *models.Material) error {
	return r.db.Create(material).Error
}

// GetAll mengambil semua materi berdasarkan class_id dan diurutkan berdasarkan minggu
func (r *materialRepository) GetAll(classID string) ([]models.Material, error) {
	var materials []models.Material
	// Filter berdasarkan class_id dan urutkan agar materi muncul berurutan dari Minggu 1
	err := r.db.Where("class_id = ?", classID).Order("week asc").Find(&materials).Error
	return materials, err
}

// GetByID mengambil satu data materi berdasarkan primary key kustom (material_id)
func (r *materialRepository) GetByID(id string) (models.Material, error) {
	var m models.Material
	// Menggunakan explicit query karena nama kolom bukan 'id'
	err := r.db.Where("material_id = ?", id).First(&m).Error
	return m, err
}

// Update memperbarui data materi yang sudah ada
func (r *materialRepository) Update(id string, m *models.Material) error {
	// r.db.Model(...) memberitahu GORM tabel mana yang akan diupdate
	// .Where(...) menentukan baris mana yang akan diupdate
	// .Updates(...) melakukan update hanya pada field yang dikirim (tidak null)
	return r.db.Model(&models.Material{}).Where("material_id = ?", id).Updates(m).Error
}

// Delete menghapus data materi dari database
func (r *materialRepository) Delete(id string) error {
	// Menghapus data secara permanen berdasarkan material_id
	return r.db.Where("material_id = ?", id).Delete(&models.Material{}).Error
}