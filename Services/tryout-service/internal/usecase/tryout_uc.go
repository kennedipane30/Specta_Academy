package usecase

import (
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
)

type TryoutUsecase interface {
	ProcessSync(tryout models.Tryout, questions []models.Question) error
	ProcessSubmissionsSync(subs []models.TryoutSubmission) error
	// ✨ PASTIKAN DUA BARIS DI BAWAH INI ADA DI DALAM INTERFACE
	FetchTryoutsByClass(classID uint) ([]models.Tryout, error)
	FetchQuestionsByTryout(tryoutID uint) ([]models.Question, error)
}

type tryoutUC struct {
	repo repository.TryoutRepository
}

func NewTryoutUsecase(repo repository.TryoutRepository) TryoutUsecase {
	return &tryoutUC{repo}
}

func (uc *tryoutUC) ProcessSync(t models.Tryout, qs []models.Question) error {
	return uc.repo.SyncFullPackage(t, qs)
}

func (uc *tryoutUC) ProcessSubmissionsSync(subs []models.TryoutSubmission) error {
	return uc.repo.SyncSubmissions(subs)
}

// ✨ Implementasi fungsi FetchTryoutsByClass
func (uc *tryoutUC) FetchTryoutsByClass(classID uint) ([]models.Tryout, error) {
	return uc.repo.GetByClass(classID)
}

// ✨ Implementasi fungsi FetchQuestionsByTryout
func (uc *tryoutUC) FetchQuestionsByTryout(tryoutID uint) ([]models.Question, error) {
	return uc.repo.GetQuestions(tryoutID)
}