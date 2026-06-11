package repository

import (
	"strconv"
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	SyncFullPackage(t *models.Tryout, qs []models.Question) error
	SyncSubmissions(s *models.TryoutSubmission) error
	GetByClass(classID string, userID string) ([]map[string]interface{}, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
	GetHistory(userID string) ([]models.HistoryResponse, error)
	GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error)
	DeleteTryout(tryoutID string) error  // ✅ TAMBAH INI
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

func (r *tryoutRepository) SyncFullPackage(t *models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Create(t).Error; err != nil {
			return err
		}

		if len(qs) > 0 {
			for i := range qs {
				qs[i].TryoutID = t.TryoutID
				qs[i].ClassID = t.ClassID
			}
			if err := tx.Create(&qs).Error; err != nil {
				return err
			}
		}
		return nil
	})
}

func (r *tryoutRepository) SyncSubmissions(s *models.TryoutSubmission) error {
	var existing models.TryoutSubmission
	err := r.db.Where("user_id = ? AND tryout_id = ?", s.UserID, s.TryoutID).First(&existing).Error
	
	if err == nil {
		existing.Answers = s.Answers
		existing.Score = s.Score
		return r.db.Save(&existing).Error
	}
	
	return r.db.Create(s).Error
}

func (r *tryoutRepository) GetByClass(classID string, userID string) ([]map[string]interface{}, error) {
	var tryouts []models.Tryout
	
	if classID == "" || classID == "0" {
		if err := r.db.Find(&tryouts).Error; err != nil {
			return nil, err
		}
	} else {
		if err := r.db.Where("class_id = ?", classID).Find(&tryouts).Error; err != nil {
			return nil, err
		}
	}

	var results []map[string]interface{}
	for _, t := range tryouts {
		var submission models.TryoutSubmission
		err := r.db.Where("tryout_id = ? AND user_id = ?", t.TryoutID, userID).First(&submission).Error

		isDone := false
		score := "-"

		if err == nil {
			isDone = true
			score = strconv.FormatFloat(submission.Score, 'f', 0, 64)
		}

		item := map[string]interface{}{
			"tryout_id":       t.TryoutID,
			"class_id":        t.ClassID,
			"title":           t.Title,
			"duration":        t.Duration,
			"total_questions": t.TotalQuestions,
			"is_done":         isDone,
			"score":           score,
		}
		results = append(results, item)
	}
	return results, nil
}

func (r *tryoutRepository) GetQuestions(tryoutID string) ([]models.Question, error) {
	var data []models.Question
	err := r.db.Where("tryout_id = ?", tryoutID).Order("question_id asc").Find(&data).Error
	return data, err
}

func (r *tryoutRepository) GetHistory(userID string) ([]models.HistoryResponse, error) {
	var results []models.HistoryResponse

	err := r.db.Table("tryout_submissions").
		Select("tryout_submissions.tryout_id, tryouts.title as tryout_title, tryout_submissions.score, tryout_submissions.submitted_at").
		Joins("left join tryouts on tryouts.tryout_id = tryout_submissions.tryout_id").
		Where("tryout_submissions.user_id = ?", userID).
		Order("tryout_submissions.submitted_at DESC").
		Limit(7).
		Scan(&results).Error

	return results, err
}

func (r *tryoutRepository) GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error) {
	var submissions []models.TryoutSubmission
	err := r.db.Where("tryout_id = ?", tryoutID).Order("submitted_at DESC").Find(&submissions).Error
	return submissions, err
}

// ✅ TAMBAH: DeleteTryout - Menghapus paket tryout beserta semua relasinya
func (r *tryoutRepository) DeleteTryout(tryoutID string) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		// Hapus submissions terkait
		if err := tx.Where("tryout_id = ?", tryoutID).Delete(&models.TryoutSubmission{}).Error; err != nil {
			return err
		}
		// Hapus questions terkait
		if err := tx.Where("tryout_id = ?", tryoutID).Delete(&models.Question{}).Error; err != nil {
			return err
		}
		// Hapus tryout
		if err := tx.Where("tryout_id = ?", tryoutID).Delete(&models.Tryout{}).Error; err != nil {
			return err
		}
		return nil
	})
}