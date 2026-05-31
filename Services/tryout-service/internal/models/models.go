package models

import "time"

// Model untuk Tabel tryouts
type Tryout struct {
	TryoutID       uint      `gorm:"primaryKey;column:tryout_id" json:"tryout_id"`
	ClassID        uint      `gorm:"column:class_id" json:"class_id"`
	Title          string    `gorm:"column:title" json:"title"`
	Duration       int       `gorm:"column:duration_minutes" json:"duration"` // Mapping ke duration_minutes
	TotalQuestions int       `gorm:"column:total_questions" json:"total_questions"`
	Status         string    `gorm:"column:status" json:"status"`
	IsActive       bool      `gorm:"column:is_active;default:true" json:"is_active"`
	CreatedAt      time.Time `json:"created_at"`
	UpdatedAt      time.Time `json:"updated_at"`
}

// Model untuk Tabel questions
type Question struct {
	QuestionID    uint      `gorm:"primaryKey;column:question_id" json:"question_id"`
	TryoutID      uint      `gorm:"column:tryout_id" json:"tryout_id"`
	ClassID       uint      `gorm:"column:class_id" json:"class_id"`
	SubjectName   string    `gorm:"column:subject_name" json:"subject_name"`
	Question      string    `gorm:"column:question" json:"question"`
	QuestionImage string    `gorm:"column:question_image" json:"question_image"`
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionE       string    `gorm:"column:option_e" json:"option_e"`
	CorrectAnswer string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt     time.Time `json:"created_at"`
	UpdatedAt     time.Time `json:"updated_at"`
}

// ✨ MODEL UTAMA HASIL (TryoutResult)
type TryoutResult struct {
	ResultID     uint      `gorm:"primaryKey;column:result_id" json:"result_id"`
	UserID       uint      `gorm:"column:user_id" json:"user_id"`
	TryoutID     uint      `gorm:"column:tryout_id" json:"tryout_id"`
	Score        int       `gorm:"column:score" json:"score"`
	TotalCorrect int       `gorm:"column:total_correct" json:"total_correct"`
	CreatedAt    time.Time `gorm:"column:created_at" json:"created_at"`
	UpdatedAt    time.Time `gorm:"column:updated_at" json:"updated_at"`
}

// ✨ PENTING: Memaksa GORM menggunakan nama tabel sesuai pgAdmin Anda
func (TryoutResult) TableName() string {
	return "tryout_results"
}

// Model untuk Tabel tryout_submissions (Riwayat Jawaban Detail)
type TryoutSubmission struct {
	ID          uint      `gorm:"primaryKey;column:id" json:"id"`
	UserID      uint      `gorm:"column:user_id" json:"user_id"`
	TryoutID    uint      `gorm:"column:tryout_id" json:"tryout_id"`
	Answers     string    `gorm:"type:text;column:answers" json:"answers"`
	Score       float64   `gorm:"column:score" json:"score"`
	SubmittedAt time.Time `gorm:"column:submitted_at" json:"submitted_at"`
}
