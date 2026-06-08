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
	
	// ✨ TAMBAHKAN INI
	GetHistory(userID string) ([]models.HistoryResponse, error)
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

func (r *tryoutRepository) SyncFullPackage(t *models.Tryout, qs []models.Question) error {
	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Save(t).Error; err != nil { return err }
		tx.Where("tryout_id = ?", t.TryoutID).Delete(&models.Question{})
		if len(qs) > 0 {
			for i := range qs { qs[i].TryoutID = t.TryoutID }
			if err := tx.Create(&qs).Error; err != nil { return err }
		}
		return nil
	})
}

func (r *tryoutRepository) SyncSubmissions(s *models.TryoutSubmission) error {
	return r.db.Create(s).Error
}

func (r *tryoutRepository) GetByClass(classID string, userID string) ([]map[string]interface{}, error) {
	var tryouts []models.Tryout
	if err := r.db.Where("class_id = ?", classID).Find(&tryouts).Error; err != nil {
		return nil, err
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

// ✨ FUNGSI BARU: Ambil 7 Riwayat Terakhir dengan join ke tabel Tryouts untuk ambil judulnya
func (r *tryoutRepository) GetHistory(userID string) ([]models.HistoryResponse, error) {
	var results []models.HistoryResponse
	
	err := r.db.Table("tryout_submissions").
		Select("tryout_submissions.tryout_id, tryouts.title as tryout_title, tryout_submissions.score, tryout_submissions.submitted_at").
		Joins("left join tryouts on tryouts.tryout_id = tryout_submissions.tryout_id").
		Where("tryout_submissions.user_id = ?", userID).
		Order("tryout_submissions.submitted_at DESC").
		Limit(7). // Hanya ambil 7 data terbaru
		Scan(&results).Error

	return results, err
}