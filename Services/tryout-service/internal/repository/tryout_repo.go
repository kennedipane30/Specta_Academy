package repository

import (
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(t *models.Tryout, qs []models.Question) error
	SyncSubmissions(s *models.TryoutSubmission) error
	GetByClass(classID string) ([]models.Tryout, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

// 1. Simpan Paket TO lengkap
func (r *tryoutRepository) SyncFullPackage(t *models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(t).Error; err != nil {
			return err
		}
		tx.Where("tryout_id = ?", t.TryoutID).Delete(&models.Question{})
		if len(qs) > 0 {
			for i := range qs {
				qs[i].TryoutID = t.TryoutID
			}
			if err := tx.Create(&qs).Error; err != nil {
				return err
			}
		}
		return nil
	})
}

// 2. ✨ SIMPAN RIWAYAT (DARI LARAVEL KE DB GO)
func (r *tryoutRepository) SyncSubmissions(s *models.TryoutSubmission) error {
	// Memasukkan baris baru ke tabel tryout_submissions
	return r.db.Create(s).Error
}

// 3. Ambil Daftar TO
func (r *tryoutRepository) GetByClass(classID string) ([]models.Tryout, error) {
	var data []models.Tryout
	err := r.db.Where("class_id = ?", classID).Find(&data).Error
	return data, err
}

// 4. Ambil Daftar Soal
func (r *tryoutRepository) GetQuestions(tryoutID string) ([]models.Question, error) {
	var data []models.Question
	err := r.db.Where("tryout_id = ?", tryoutID).Order("question_id asc").Find(&data).Error
	return data, err
}