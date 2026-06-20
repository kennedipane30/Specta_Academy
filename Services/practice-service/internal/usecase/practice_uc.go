package usecase

import (
	"practice-service/internal/models"
	"practice-service/internal/repository"
)

type PracticeUsecase interface {
	SyncQuestions(questions []models.PracticeQuestion) error
	GetListByClass(classID uint) ([]models.PracticeQuestion, error) 
	GetList(classID uint, subject string, week int) ([]models.PracticeQuestion, error)
	RemoveWeek(classID uint, subject string, week int) error
}

type practiceUC struct {
	repo repository.PracticeRepository
}

func NewPracticeUsecase(repo repository.PracticeRepository) PracticeUsecase {
	return &practiceUC{repo}
}

func (uc *practiceUC) SyncQuestions(questions []models.PracticeQuestion) error {
	return uc.repo.BulkInsert(questions)
}

func (uc *practiceUC) GetListByClass(classID uint) ([]models.PracticeQuestion, error) {
	return uc.repo.GetByClass(classID)
}

func (uc *practiceUC) GetList(classID uint, subject string, week int) ([]models.PracticeQuestion, error) {
	return uc.repo.GetByWeek(classID, subject, week)
}

func (uc *practiceUC) RemoveWeek(classID uint, subject string, week int) error {
	return uc.repo.DeleteByWeek(classID, subject, week)
}