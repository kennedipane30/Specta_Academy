package repository

import (
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(tryout models.Tryout, questions []models.Question) error
	SyncSubmissions(subs []models.TryoutSubmission) error
	// ✨ PASTIKAN DUA BARIS DI BAWAH INI ADA DI DALAM INTERFACE
	GetByClass(classID uint) ([]models.Tryout, error)
	GetQuestions(tryoutID uint) ([]models.Question, error)
}

type tryoutRepo struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepo{db}
}

func (r *tryoutRepo) SyncFullPackage(t models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(&t).Error; err != nil { return err }
		tx.Where("tryout_id = ?", t.TryoutID).Delete(&models.Question{})
		if len(qs) > 0 {
			if err := tx.Create(&qs).Error; err != nil { return err }
		}
		return nil
	})
}

func (r *tryoutRepo) SyncSubmissions(subs []models.TryoutSubmission) error {
	return r.db.Save(&subs).Error
}

// ✨ Implementasi GetByClass
func (r *tryoutRepo) GetByClass(classID uint) ([]models.Tryout, error) {
	var tryouts []models.Tryout
	err := r.db.Where("class_id = ?", classID).Find(&tryouts).Error
	return tryouts, err
}

// ✨ Implementasi GetQuestions
func (r *tryoutRepo) GetQuestions(tryoutID uint) ([]models.Question, error) {
	var questions []models.Question
	err := r.db.Where("tryout_id = ?", tryoutID).Find(&questions).Error
	return questions, err
}