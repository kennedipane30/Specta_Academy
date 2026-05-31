package usecase

import (
	"tryout-service/internal/models"
	"tryout-service/internal/repository"
)

type TryoutUsecase interface {
	SyncTryout(t models.Tryout, qs []models.Question) error
	SyncSubmissions(s models.TryoutSubmission) error
	GetTryouts(classID string) ([]models.Tryout, error)
	GetQuestions(tryoutID string) ([]models.Question, error)
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

// ✨ Menghubungkan Handler ke Repository
func (u *tryoutUsecase) SyncSubmissions(s models.TryoutSubmission) error {
	return u.repo.SyncSubmissions(&s)
}

func (u *tryoutUsecase) GetTryouts(classID string) ([]models.Tryout, error) {
	return u.repo.GetByClass(classID)
}

func (u *tryoutUsecase) GetQuestions(tryoutID string) ([]models.Question, error) {
	return u.repo.GetQuestions(tryoutID)
}