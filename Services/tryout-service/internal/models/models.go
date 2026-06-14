package models

import "time"

type Tryout struct {
	TryoutID       uint      `gorm:"primaryKey;autoIncrement;column:tryout_id" json:"tryout_id"`
	ClassID        uint      `gorm:"column:class_id;not null" json:"class_id"`
	Title          string    `gorm:"column:title;not null" json:"title"`
	Duration       int       `gorm:"column:duration_minutes" json:"duration"`
	TotalQuestions int       `gorm:"column:total_questions" json:"total_questions"`
	Status         string    `gorm:"column:status" json:"status"`
	IsActive       bool      `gorm:"column:is_active;default:true" json:"is_active"`
	CreatedAt      time.Time `gorm:"column:created_at;autoCreateTime" json:"created_at"`
	UpdatedAt      time.Time `gorm:"column:updated_at;autoUpdateTime" json:"updated_at"`
}

func (Tryout) TableName() string {
	return "tryouts"
}

type Question struct {
	QuestionID    uint      `gorm:"primaryKey;autoIncrement;column:question_id" json:"question_id"`
	TryoutID      uint      `gorm:"column:tryout_id;not null" json:"tryout_id"`
	ClassID       uint      `gorm:"column:class_id;not null" json:"class_id"`
	SubjectName   string    `gorm:"column:subject_name;not null" json:"subject_name"`
	Question      string    `gorm:"column:question;not null" json:"question"`
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionE       string    `gorm:"column:option_e" json:"option_e"`
	CorrectAnswer string    `gorm:"column:correct_answer" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation" json:"explanation"`
	CreatedAt     time.Time `gorm:"column:created_at;autoCreateTime" json:"created_at"`
	UpdatedAt     time.Time `gorm:"column:updated_at;autoUpdateTime" json:"updated_at"`
}

func (Question) TableName() string {
	return "tryout_questions"
}

type TryoutSubmission struct {
	ID          uint      `gorm:"primaryKey;autoIncrement;column:id" json:"id"`
	UserID      uint      `gorm:"column:user_id;not null" json:"user_id"`
	TryoutID    uint      `gorm:"column:tryout_id;not null" json:"tryout_id"`
	Answers     string    `gorm:"type:text;column:answers" json:"answers"`
	Score       float64   `gorm:"column:score" json:"score"`
	SubmittedAt time.Time `gorm:"column:submitted_at;autoCreateTime" json:"submitted_at"`
}

func (TryoutSubmission) TableName() string {
	return "tryout_submissions"
}

// ✅ BARU: Model TryoutDraft untuk menyimpan soal sementara
type TryoutDraft struct {
	ID            uint      `gorm:"primaryKey;autoIncrement;column:id" json:"id"`
	ClassID       uint      `gorm:"column:class_id;not null" json:"class_id"`
	UserID        uint      `gorm:"column:user_id;not null" json:"user_id"`
	SubjectName   string    `gorm:"column:subject_name;not null" json:"subject_name"`
	Question      string    `gorm:"column:question;type:text;not null" json:"question"`
	OptionA       string    `gorm:"column:option_a" json:"option_a"`
	OptionB       string    `gorm:"column:option_b" json:"option_b"`
	OptionC       string    `gorm:"column:option_c" json:"option_c"`
	OptionD       string    `gorm:"column:option_d" json:"option_d"`
	OptionE       string    `gorm:"column:option_e" json:"option_e"`
	CorrectAnswer string    `gorm:"column:correct_answer;size:5" json:"correct_answer"`
	Explanation   string    `gorm:"column:explanation;type:text" json:"explanation"`
	CreatedAt     time.Time `gorm:"column:created_at;autoCreateTime" json:"created_at"`
	UpdatedAt     time.Time `gorm:"column:updated_at;autoUpdateTime" json:"updated_at"`
}

func (TryoutDraft) TableName() string {
	return "tryout_drafts"
}

type HistoryResponse struct {
	TryoutID    uint    `json:"tryout_id"`
	TryoutTitle string  `json:"title"`
	Score       float64 `json:"score"`
	SubmittedAt string  `json:"submitted_at"`
}

type SyncRequest struct {
	Tryout    Tryout     `json:"tryout"`
	Questions []Question `json:"questions"`
}

// ✅ BARU: DraftRequest untuk create/update draft
type DraftRequest struct {
	ID            uint   `json:"id"`
	ClassID       uint   `json:"class_id"`
	UserID        uint   `json:"user_id"`
	SubjectName   string `json:"subject_name"`
	Question      string `json:"question"`
	OptionA       string `json:"option_a"`
	OptionB       string `json:"option_b"`
	OptionC       string `json:"option_c"`
	OptionD       string `json:"option_d"`
	OptionE       string `json:"option_e"`
	CorrectAnswer string `json:"correct_answer"`
	Explanation   string `json:"explanation"`
}