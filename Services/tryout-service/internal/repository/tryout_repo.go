package repository

import (
	"fmt"
	"strconv"
	"tryout-service/internal/models"
	"gorm.io/gorm"
)

type TryoutRepository interface {
	// Tryout & Question
	SyncFullPackage(t *models.Tryout, qs []models.Question) error
	GetByClass(classID string, userID string) ([]map[string]interface{}, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
	DeleteTryout(tryoutID string) error

	// Submission
	SyncSubmissions(s *models.TryoutSubmission) error
	GetHistory(userID string) ([]models.HistoryResponse, error)
	GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error)

	// Draft methods
	CreateDraft(draft *models.TryoutDraft) error
	UpdateDraft(draft *models.TryoutDraft) error
	DeleteDraft(draftID string) error
	DeleteAllDrafts(classID string, userID uint, subjectName string) error
	GetDraftsByClassAndSubject(classID string, userID uint, subjectName string) ([]models.TryoutDraft, error)
	GetDraftsByClass(classID string) ([]models.TryoutDraft, error)
	GetDraftByID(draftID string) (*models.TryoutDraft, error)
	GetDraftCount(classID string, userID uint, subjectName string) (int64, error)
	GetAllDrafts() ([]models.TryoutDraft, error)
	GetDraftsByUser(userIDStr string) ([]models.TryoutDraft, error)
	GetDraftCountByUser(userID uint) (int64, error)
}

type tryoutRepository struct {
	db *gorm.DB
}

func NewTryoutRepository(db *gorm.DB) TryoutRepository {
	return &tryoutRepository{db: db}
}

// ==================== TRYOUT & QUESTION ====================

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

func (r *tryoutRepository) DeleteTryout(tryoutID string) error {
	id, err := strconv.ParseUint(tryoutID, 10, 32)
	if err != nil {
		return err
	}

	return r.db.Transaction(func(tx *gorm.DB) error {
		if err := tx.Where("tryout_id = ?", id).Delete(&models.TryoutSubmission{}).Error; err != nil {
			return fmt.Errorf("gagal hapus submissions: %w", err)
		}
		if err := tx.Where("tryout_id = ?", id).Delete(&models.Question{}).Error; err != nil {
			return fmt.Errorf("gagal hapus questions: %w", err)
		}
		if err := tx.Where("tryout_id = ?", id).Delete(&models.Tryout{}).Error; err != nil {
			return fmt.Errorf("gagal hapus tryout: %w", err)
		}
		return nil
	})
}

// ==================== SUBMISSION ====================

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

// ==================== DRAFT METHODS ====================

func (r *tryoutRepository) CreateDraft(draft *models.TryoutDraft) error {
	return r.db.Create(draft).Error
}

func (r *tryoutRepository) UpdateDraft(draft *models.TryoutDraft) error {
	return r.db.Save(draft).Error
}

func (r *tryoutRepository) DeleteDraft(draftID string) error {
	return r.db.Where("id = ?", draftID).Delete(&models.TryoutDraft{}).Error
}

func (r *tryoutRepository) DeleteAllDrafts(classID string, userID uint, subjectName string) error {
	query := r.db.Model(&models.TryoutDraft{})
	
	if classID != "" {
		query = query.Where("class_id = ?", classID)
	}
	if userID != 0 {
		query = query.Where("user_id = ?", userID)
	}
	if subjectName != "" {
		query = query.Where("subject_name = ?", subjectName)
	}
	
	return query.Delete(&models.TryoutDraft{}).Error
}

func (r *tryoutRepository) GetDraftsByClassAndSubject(classID string, userID uint, subjectName string) ([]models.TryoutDraft, error) {
	var drafts []models.TryoutDraft
	err := r.db.Where("class_id = ? AND user_id = ? AND subject_name = ?", classID, userID, subjectName).
		Order("created_at DESC").
		Find(&drafts).Error
	return drafts, err
}

func (r *tryoutRepository) GetDraftsByClass(classID string) ([]models.TryoutDraft, error) {
	var drafts []models.TryoutDraft
	err := r.db.Where("class_id = ?", classID).Find(&drafts).Error
	return drafts, err
}

func (r *tryoutRepository) GetDraftByID(draftID string) (*models.TryoutDraft, error) {
	var draft models.TryoutDraft
	err := r.db.Where("id = ?", draftID).First(&draft).Error
	if err != nil {
		return nil, err
	}
	return &draft, nil
}

func (r *tryoutRepository) GetDraftCount(classID string, userID uint, subjectName string) (int64, error) {
	var count int64
	query := r.db.Model(&models.TryoutDraft{}).Where("class_id = ? AND user_id = ?", classID, userID)
	if subjectName != "" {
		query = query.Where("subject_name = ?", subjectName)
	}
	err := query.Count(&count).Error
	return count, err
}

func (r *tryoutRepository) GetAllDrafts() ([]models.TryoutDraft, error) {
	var drafts []models.TryoutDraft
	err := r.db.Order("created_at DESC").Find(&drafts).Error
	return drafts, err
}

func (r *tryoutRepository) GetDraftsByUser(userIDStr string) ([]models.TryoutDraft, error) {
	var drafts []models.TryoutDraft
	userID, err := strconv.ParseUint(userIDStr, 10, 32)
	if err != nil {
		return nil, err
	}
	err = r.db.Where("user_id = ?", uint(userID)).Order("created_at DESC").Find(&drafts).Error
	return drafts, err
}

func (r *tryoutRepository) GetDraftCountByUser(userID uint) (int64, error) {
	var count int64
	err := r.db.Model(&models.TryoutDraft{}).Where("user_id = ?", userID).Count(&count).Error
	return count, err
}