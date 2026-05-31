package usecase

import (
	"materi-service/internal/models"
	"materi-service/internal/repository"
)

// MaterialUsecase mendefinisikan kontrak logika bisnis untuk materi
type MaterialUsecase interface {
	SyncMaterial(material *models.Material) error
	GetMaterials(classID string) ([]models.Material, error)
	GetMaterialByID(id string) (models.Material, error)
	UpdateMaterial(id string, material *models.Material) error
	DeleteMaterial(id string) error
}

type materialUsecase struct {
	repo repository.MaterialRepository
}

// NewMaterialUsecase membuat instance baru dari materialUsecase
func NewMaterialUsecase(repo repository.MaterialRepository) MaterialUsecase {
	return &materialUsecase{
		repo: repo,
	}
}

// SyncMaterial menangani penyimpanan materi baru hasil sinkronisasi dari Laravel
func (u *materialUsecase) SyncMaterial(m *models.Material) error {
	return u.repo.Save(m)
}

// GetMaterials mengambil daftar materi berdasarkan ID Kelas
func (u *materialUsecase) GetMaterials(classID string) ([]models.Material, error) {
	return u.repo.GetAll(classID)
}

// GetMaterialByID mengambil detail satu materi berdasarkan ID
func (u *materialUsecase) GetMaterialByID(id string) (models.Material, error) {
	return u.repo.GetByID(id)
}

// UpdateMaterial menangani logika pembaruan data materi
func (u *materialUsecase) UpdateMaterial(id string, m *models.Material) error {
	// Anda bisa menambahkan logika validasi tambahan di sini jika diperlukan
	return u.repo.Update(id, m)
}

// DeleteMaterial menangani logika penghapusan materi
func (u *materialUsecase) DeleteMaterial(id string) error {
	// Pastikan data ada sebelum dihapus (opsional)
	_, err := u.repo.GetByID(id)
	if err != nil {
		return err
	}
	
	return u.repo.Delete(id)
}