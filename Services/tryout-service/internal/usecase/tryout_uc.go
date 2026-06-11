package usecase

import (
	"strconv"
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
)

type TryoutUsecase interface {
	SyncTryout(t models.Tryout, qs []models.Question) error
	SyncSubmissions(s models.TryoutSubmission) error
	GetTryouts(classID string, userID string) ([]map[string]interface{}, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
	CalculateScore(tryoutIDStr string, userAnswers map[string]string) (int, int, error) 
	GetHistory(userID string) ([]models.HistoryResponse, error)
	GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error)
	DeleteTryout(tryoutID string) error  // ✅ TAMBAH INI
}

type tryoutUsecase struct {
	repo repository.TryoutRepository
}

func NewTryoutUsecase(repo repository.TryoutRepository) TryoutUsecase {
	return &tryoutUsecase{repo: repo}
}

func (u *tryoutUsecase) SyncTryout(t models.Tryout, qs []models.Question) error {
	return u.repo.SyncFullPackage(&t, qs)
}

func (u *tryoutUsecase) SyncSubmissions(s models.TryoutSubmission) error {
	return u.repo.SyncSubmissions(&s)
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

func (u *tryoutUsecase) GetHistory(userID string) ([]models.HistoryResponse, error) {
	return u.repo.GetHistory(userID)
}

func (u *tryoutUsecase) GetSubmissionsByTryout(tryoutID string) ([]models.TryoutSubmission, error) {
	return u.repo.GetSubmissionsByTryout(tryoutID)
}

// ✅ TAMBAH: DeleteTryout
func (u *tryoutUsecase) DeleteTryout(tryoutID string) error {
	return u.repo.DeleteTryout(tryoutID)
}