package models

import "time"

type Tryout struct {
	TryoutID       uint      `gorm:"primaryKey;autoIncrement;column:tryout_id" json:"tryout_id"` // ✅ TAMBAH autoIncrement
	ClassID        uint      `gorm:"column:class_id;not null" json:"class_id"` // ✅ TAMBAH not null
	Title          string    `gorm:"column:title;not null" json:"title"` // ✅ TAMBAH not null
	Duration       int       `gorm:"column:duration_minutes" json:"duration"` 
	TotalQuestions int       `gorm:"column:total_questions" json:"total_questions"`
	Status         string    `gorm:"column:status" json:"status"`
	IsActive       bool      `gorm:"column:is_active;default:true" json:"is_active"`
	CreatedAt      time.Time `gorm:"column:created_at;autoCreateTime" json:"created_at"` // ✅ TAMBAH autoCreateTime
	UpdatedAt      time.Time `gorm:"column:updated_at;autoUpdateTime" json:"updated_at"` // ✅ TAMBAH autoUpdateTime
}

type Question struct {
	QuestionID    uint      `gorm:"primaryKey;autoIncrement;column:question_id" json:"question_id"` // ✅ TAMBAH autoIncrement
	TryoutID      uint      `gorm:"column:tryout_id;not null" json:"tryout_id"` // ✅ TAMBAH not null
	ClassID       uint      `gorm:"column:class_id;not null" json:"class_id"` // ✅ TAMBAH not null
	SubjectName   string    `gorm:"column:subject_name;not null" json:"subject_name"` // ✅ TAMBAH not null
	Question      string    `gorm:"column:question;not null" json:"question"` // ✅ TAMBAH not null
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionE       string    `gorm:"column:option_e" json:"option_e"`
	CorrectAnswer string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt     time.Time `gorm:"column:created_at;autoCreateTime" json:"created_at"` // ✅ TAMBAH autoCreateTime
	UpdatedAt     time.Time `gorm:"column:updated_at;autoUpdateTime" json:"updated_at"` // ✅ TAMBAH autoUpdateTime
}

type TryoutSubmission struct {
	ID          uint      `gorm:"primaryKey;autoIncrement;column:id" json:"id"` // ✅ TAMBAH autoIncrement
	UserID      uint      `gorm:"column:user_id;not null" json:"user_id"` // ✅ TAMBAH not null
	TryoutID    uint      `gorm:"column:tryout_id;not null" json:"tryout_id"` // ✅ TAMBAH not null
	Answers     string    `gorm:"type:text;column:answers" json:"answers"`
	Score       float64   `gorm:"column:score" json:"score"`
	SubmittedAt time.Time `gorm:"column:submitted_at;autoCreateTime" json:"submitted_at"` // ✅ TAMBAH autoCreateTime
}

// ✨ STRUCT BARU: Untuk format balasan riwayat nilai ke Flutter
type HistoryResponse struct {
	TryoutID    uint    `json:"tryout_id"`
	TryoutTitle string  `json:"title"`
	Score       float64 `json:"score"`
	SubmittedAt string  `json:"submitted_at"`
}

// ✅ TAMBAH: SyncRequest untuk menerima data dari Laravel (tanpa ID)
type SyncRequest struct {
	Tryout    Tryout     `json:"tryout"`
	Questions []Question `json:"questions"`
}