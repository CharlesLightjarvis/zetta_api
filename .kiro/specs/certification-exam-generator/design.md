# Design Document

## Overview

Le système de génération d'examens de certification s'appuie sur l'architecture Laravel existante et étend les modèles actuels pour supporter une nouvelle entité `Chapter` qui organise les questions par thématiques. Le système permet de configurer des examens personnalisés et de générer des questions aléatoires selon la configuration définie.

## Architecture

### Database Schema Extensions

#### Nouvelle table `chapters`
```sql
- id (UUID, primary key)
- certification_id (UUID, foreign key vers certifications)
- name (string)
- description (text, nullable)
- order (integer) - pour l'ordre d'affichage
- created_at, updated_at (timestamps)
```

#### Modification table `questions`
- Ajout de `chapter_id` (UUID, foreign key vers chapters)
- Suppression du système polymorphique `questionable` pour les questions de certification
- Conservation du système polymorphique pour d'autres usages (modules, lessons)

#### Extension table `quiz_configurations`
- Modification de `module_distribution` vers `chapter_distribution` (JSON)
- Format: `{"chapter_id_1": 5, "chapter_id_2": 10, ...}`

### Relations Eloquent

```php
// Certification
public function chapters(): HasMany
public function quizConfiguration(): MorphOne (existant)

// Chapter (nouveau modèle)
public function certification(): BelongsTo
public function questions(): HasMany

// Question (modification)
public function chapter(): BelongsTo (pour questions de certification)
public function questionable(): MorphTo (conservé pour autres usages)
```

## Components and Interfaces

### Models

#### Chapter Model
```php
class Chapter extends Model
{
    protected $fillable = ['certification_id', 'name', 'description', 'order'];
    
    public function certification(): BelongsTo
    public function questions(): HasMany
    public function getQuestionsCountAttribute(): int
}
```

#### Certification Model (extension)
```php
// Ajout de la relation
public function chapters(): HasMany
{
    return $this->hasMany(Chapter::class)->orderBy('order');
}
```

#### Question Model (modification)
```php
// Ajout de la relation chapter pour les questions de certification
public function chapter(): BelongsTo
{
    return $this->belongsTo(Chapter::class);
}

// Scope pour les questions de certification
public function scopeCertificationQuestions($query)
{
    return $query->whereNotNull('chapter_id');
}
```

### Controllers

#### ChapterController
```php
class ChapterController extends Controller
{
    public function index(Certification $certification) // Liste des chapitres
    public function store(Certification $certification, ChapterRequest $request) // Créer chapitre
    public function update(Chapter $chapter, ChapterRequest $request) // Modifier chapitre
    public function destroy(Chapter $chapter) // Supprimer chapitre
}
```

#### CertificationQuestionController
```php
class CertificationQuestionController extends Controller
{
    public function index(Chapter $chapter) // Questions d'un chapitre
    public function store(Chapter $chapter, QuestionRequest $request) // Ajouter question
    public function update(Question $question, QuestionRequest $request) // Modifier question
    public function destroy(Question $question) // Supprimer question
}
```

#### ExamConfigurationController
```php
class ExamConfigurationController extends Controller
{
    public function show(Certification $certification) // Afficher config actuelle
    public function update(Certification $certification, ConfigRequest $request) // Sauvegarder config
}
```

#### ExamGeneratorController
```php
class ExamGeneratorController extends Controller
{
    public function generate(Certification $certification) // Générer examen
    public function start(Certification $certification) // Démarrer examen
    public function submit(ExamSession $session, SubmitRequest $request) // Soumettre réponses
}
```

### Services

#### ExamGeneratorService
```php
class ExamGeneratorService
{
    public function generateExam(Certification $certification): array
    {
        // 1. Récupérer la configuration d'examen
        // 2. Pour chaque chapitre configuré, sélectionner N questions aléatoires
        // 3. Mélanger l'ordre des questions
        // 4. Mélanger l'ordre des réponses pour chaque question
        // 5. Retourner l'examen structuré
    }
    
    private function selectRandomQuestions(Chapter $chapter, int $count): Collection
    private function shuffleAnswers(Question $question): Question
}
```

#### ExamSessionService
```php
class ExamSessionService
{
    public function createSession(User $user, Certification $certification): ExamSession
    public function saveAnswer(ExamSession $session, string $questionId, array $answers): void
    public function calculateScore(ExamSession $session): int
    public function submitExam(ExamSession $session): ExamResult
}
```

## Data Models

### Chapter
```php
[
    'id' => 'uuid',
    'certification_id' => 'uuid',
    'name' => 'string',
    'description' => 'text|null',
    'order' => 'integer',
    'questions_count' => 'integer' // computed
]
```

### ExamConfiguration (extension de QuizConfiguration)
```php
[
    'configurable_type' => 'App\Models\Certification',
    'configurable_id' => 'certification_uuid',
    'total_questions' => 'integer',
    'chapter_distribution' => [
        'chapter_uuid_1' => 5,
        'chapter_uuid_2' => 10,
        // ...
    ],
    'time_limit' => 'integer', // minutes
    'passing_score' => 'integer' // pourcentage
]
```

### GeneratedExam
```php
[
    'certification_id' => 'uuid',
    'questions' => [
        [
            'id' => 'question_uuid',
            'chapter_name' => 'string',
            'question' => 'string',
            'answers' => [
                ['id' => 1, 'text' => 'Réponse A'],
                ['id' => 2, 'text' => 'Réponse B'],
                // ordre mélangé
            ],
            'points' => 'integer'
        ]
    ],
    'time_limit' => 'integer',
    'total_points' => 'integer'
]
```

## Error Handling

### Validation Rules

#### ChapterRequest
```php
'name' => 'required|string|max:255',
'description' => 'nullable|string',
'order' => 'integer|min:1'
```

#### ExamConfigurationRequest
```php
'chapter_distribution' => 'required|array',
'chapter_distribution.*' => 'integer|min:1',
'time_limit' => 'required|integer|min:1',
'passing_score' => 'required|integer|min:1|max:100'
```

### Exception Handling
- `InsufficientQuestionsException` - Pas assez de questions dans un chapitre
- `InvalidConfigurationException` - Configuration d'examen invalide
- `ExamTimeExpiredException` - Temps d'examen dépassé

## Testing Strategy

### Unit Tests
- `ChapterTest` - CRUD operations sur les chapitres
- `ExamGeneratorServiceTest` - Génération d'examens avec différentes configurations
- `ExamSessionServiceTest` - Gestion des sessions d'examen

### Feature Tests
- `ChapterManagementTest` - Gestion complète des chapitres via API
- `ExamConfigurationTest` - Configuration d'examens via interface
- `ExamGenerationTest` - Génération et passage d'examens complets

### Integration Tests
- Test de génération d'examen avec vraies données
- Test de soumission d'examen et calcul de score
- Test de validation des contraintes (nombre de questions disponibles)

## Migration Strategy

1. **Phase 1**: Créer la table `chapters` et le modèle
2. **Phase 2**: Modifier la table `questions` pour ajouter `chapter_id`
3. **Phase 3**: Migrer les questions existantes si nécessaire
4. **Phase 4**: Adapter `quiz_configurations` pour `chapter_distribution`
5. **Phase 5**: Implémenter les nouveaux contrôleurs et services