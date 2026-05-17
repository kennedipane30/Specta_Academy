package usecase

import (
	"materi-service/internal/models"
	"materi-service/internal/repository"
)

type MaterialUsecase interface {
	Sync(materi models.Material) error
	FetchMaterialsByClass(classID uint) ([]models.Material, error)           // ✨ Tambahkan ini
	FetchMaterialsBySubject(classID uint, subjectName string) ([]models.Material, error) // ✨ Tambahkan ini
}

type materialUC struct {
	repo repository.MaterialRepository
}

func NewMaterialUsecase(repo repository.MaterialRepository) MaterialUsecase {
	return &materialUC{repo}
}

func (uc *materialUC) Sync(materi models.Material) error {
	return uc.repo.SyncMaterial(materi)
}

func (uc *materialUC) FetchMaterialsByClass(classID uint) ([]models.Material, error) {
	return uc.repo.GetByClass(classID)
}

func (uc *materialUC) FetchMaterialsBySubject(classID uint, subjectName string) ([]models.Material, error) {
	return uc.repo.GetByClassAndSubject(classID, subjectName)
}