package usecase

import (
	"strconv"
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
)

type TryoutUsecase interface {
	// Tryout & Question
	SyncTryout(t models.Tryout, qs []models.Question) error
	GetTryouts(classID string, userID string) ([]map[string]interface{}, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
	CalculateScore(tryoutIDStr string, userAnswers map[string]string) (int, int, error)
	DeleteTryout(tryoutID string) error

	// Submission
	SyncSubmissions(s models.TryoutSubmission) error
	GetHistory(userID string) ([]models.HistoryResponse, error)
	GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error)

	// Draft methods
	CreateDraft(req models.DraftRequest) error
	UpdateDraft(req models.DraftRequest) error
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

type tryoutUsecase struct {
	repo repository.TryoutRepository
}

func NewTryoutUsecase(repo repository.TryoutRepository) TryoutUsecase {
	return &tryoutUsecase{repo: repo}
}

// ==================== TRYOUT & QUESTION ====================

func (u *tryoutUsecase) SyncTryout(t models.Tryout, qs []models.Question) error {
	return u.repo.SyncFullPackage(&t, qs)
}

func (u *tryoutUsecase) GetTryouts(classID string, userID string) ([]map[string]interface{}, error) {
	return u.repo.GetByClass(classID, userID)
}

func (u *tryoutUsecase) GetQuestions(tryoutID string) ([]models.Question, error) {
	return u.repo.GetQuestions(tryoutID)
}

func (u *tryoutUsecase) CalculateScore(tryoutIDStr string, userAnswers map[string]string) (int, int, error) {
	questions, err := u.repo.GetQuestions(tryoutIDStr)
	if err != nil {
		return 0, 0, err
	}

	totalQuestions := len(questions)
	if totalQuestions == 0 {
		return 0, 0, nil
	}

	correctCount := 0
	for _, q := range questions {
		qIDStr := strconv.Itoa(int(q.QuestionID))
		userAns, exists := userAnswers[qIDStr]
		if exists && userAns == q.CorrectAnswer {
			correctCount++
		}
	}

	score := (correctCount * 100) / totalQuestions
	return score, correctCount, nil
}

func (u *tryoutUsecase) DeleteTryout(tryoutID string) error {
	return u.repo.DeleteTryout(tryoutID)
}

// ==================== SUBMISSION ====================

func (u *tryoutUsecase) SyncSubmissions(s models.TryoutSubmission) error {
	return u.repo.SyncSubmissions(&s)
}

func (u *tryoutUsecase) GetHistory(userID string) ([]models.HistoryResponse, error) {
	return u.repo.GetHistory(userID)
}

func (u *tryoutUsecase) GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error) {
	return u.repo.GetSubmissionsByTryout(tryoutID)
}

// ==================== DRAFT METHODS ====================

func (u *tryoutUsecase) CreateDraft(req models.DraftRequest) error {
	draft := &models.TryoutDraft{
		ClassID:       req.ClassID,
		UserID:        req.UserID,
		SubjectName:   req.SubjectName,
		Question:      req.Question,
		OptionA:       req.OptionA,
		OptionB:       req.OptionB,
		OptionC:       req.OptionC,
		OptionD:       req.OptionD,
		OptionE:       req.OptionE,
		CorrectAnswer: req.CorrectAnswer,
		Explanation:   req.Explanation,
	}
	return u.repo.CreateDraft(draft)
}

func (u *tryoutUsecase) UpdateDraft(req models.DraftRequest) error {
	draft := &models.TryoutDraft{
		ID:            req.ID,
		ClassID:       req.ClassID,
		UserID:        req.UserID,
		SubjectName:   req.SubjectName,
		Question:      req.Question,
		OptionA:       req.OptionA,
		OptionB:       req.OptionB,
		OptionC:       req.OptionC,
		OptionD:       req.OptionD,
		OptionE:       req.OptionE,
		CorrectAnswer: req.CorrectAnswer,
		Explanation:   req.Explanation,
	}
	return u.repo.UpdateDraft(draft)
}

func (u *tryoutUsecase) DeleteDraft(draftID string) error {
	return u.repo.DeleteDraft(draftID)
}

func (u *tryoutUsecase) DeleteAllDrafts(classID string, userID uint, subjectName string) error {
	return u.repo.DeleteAllDrafts(classID, userID, subjectName)
}

func (u *tryoutUsecase) GetDraftsByClassAndSubject(classID string, userID uint, subjectName string) ([]models.TryoutDraft, error) {
	return u.repo.GetDraftsByClassAndSubject(classID, userID, subjectName)
}

func (u *tryoutUsecase) GetDraftsByClass(classID string) ([]models.TryoutDraft, error) {
	return u.repo.GetDraftsByClass(classID)
}

func (u *tryoutUsecase) GetDraftByID(draftID string) (*models.TryoutDraft, error) {
	return u.repo.GetDraftByID(draftID)
}

func (u *tryoutUsecase) GetDraftCount(classID string, userID uint, subjectName string) (int64, error) {
	return u.repo.GetDraftCount(classID, userID, subjectName)
}

func (u *tryoutUsecase) GetAllDrafts() ([]models.TryoutDraft, error) {
	return u.repo.GetAllDrafts()
}

func (u *tryoutUsecase) GetDraftsByUser(userIDStr string) ([]models.TryoutDraft, error) {
	return u.repo.GetDraftsByUser(userIDStr)
}

func (u *tryoutUsecase) GetDraftCountByUser(userID uint) (int64, error) {
	return u.repo.GetDraftCountByUser(userID)
}