#!/bin/bash

is_conventional_commit() {
    local message="$1"
    [[ "$message" =~ ^(feat|fix|docs|style|refactor|test|chore|perf|ci|build|revert)(\(.+\))?!?:\ .+ ]]
}

log_error() {
    echo "[gacp] Error: $1" >&2
}

log_warning() {
    echo "[gacp] Warning: $1" >&2
}

log_success() {
    echo "[gacp] Success: $1"
}

generate_intelligent_message() {
    local files="$1"
    local diff_stats="$2"
    local diff_content="$3"
    local commit_type="$4"
    local file_count="$5"
    
    local message=""
    local additions deletions
    additions=$(echo "$diff_stats" | awk '{sum+=$1} END {print sum+0}')
    deletions=$(echo "$diff_stats" | awk '{sum+=$2} END {print sum+0}')
    
    # Generate specific message based on files and changes
    if echo "$files" | grep -qiE "(filament|Filament)"; then
        local filament_files=$(echo "$files" | grep -iE "(filament|Filament)" | wc -l)
        if [[ "$additions" -gt $((deletions * 3)) ]]; then
            message="implement new Filament components and features"
        elif [[ "$deletions" -gt $((additions * 2)) ]]; then
            message="remove unused Filament components"
        else
            message="update Filament admin interface"
        fi
    elif echo "$files" | grep -qE "/Models?/|Model\.php$"; then
        local model_files=$(echo "$files" | grep -E "/Models?/|Model\.php$")
        local model_count=$(echo "$model_files" | wc -l)
        if [[ "$model_count" -eq 1 ]]; then
            local model_name=$(echo "$model_files" | sed 's/.*\///g' | sed 's/\.php$//g')
            if [[ "$additions" -gt $((deletions * 3)) ]]; then
                message="add new ${model_name} model with relationships"
            elif echo "$diff_content" | grep -qiE "(fillable|guarded|casts|dates)"; then
                message="configure ${model_name} model attributes"
            elif echo "$diff_content" | grep -qiE "(belongsTo|hasMany|hasOne|belongsToMany)"; then
                message="define ${model_name} model relationships"
            else
                message="update ${model_name} model structure"
            fi
        else
            message="update multiple model definitions"
        fi
    elif echo "$files" | grep -qE "/Controllers?/|Controller\.php$"; then
        local controller_files=$(echo "$files" | grep -E "/Controllers?/|Controller\.php$")
        local controller_count=$(echo "$controller_files" | wc -l)
        if [[ "$controller_count" -eq 1 ]]; then
            local controller_name=$(echo "$controller_files" | sed 's/.*\///g' | sed 's/Controller\.php$//g')
            if [[ "$additions" -gt $((deletions * 3)) ]]; then
                message="implement ${controller_name} controller logic"
            elif echo "$diff_content" | grep -qiE "(index|show|store|update|destroy)"; then
                message="add CRUD operations to ${controller_name} controller"
            elif echo "$diff_content" | grep -qiE "(authorize|validate|request)"; then
                message="add validation and authorization to ${controller_name}"
            else
                message="refactor ${controller_name} controller methods"
            fi
        else
            message="update controller implementations"
        fi
    elif echo "$files" | grep -qE "/Services?/|Service\.php$"; then
        local service_files=$(echo "$files" | grep -E "/Services?/|Service\.php$")
        local service_count=$(echo "$service_files" | wc -l)
        if [[ "$service_count" -eq 1 ]]; then
            local service_name=$(echo "$service_files" | sed 's/.*\///g' | sed 's/Service\.php$//g')
            message="implement ${service_name} service logic"
        else
            message="update service layer implementations"
        fi
    elif echo "$files" | grep -qE "database/migrations/"; then
        local migration_files=$(echo "$files" | grep -E "database/migrations/")
        local migration_count=$(echo "$migration_files" | wc -l)
        if [[ "$migration_count" -eq 1 ]]; then
            local migration_name=$(echo "$migration_files" | sed 's/.*_//g' | sed 's/\.php$//g')
            message="add ${migration_name} database migration"
        else
            message="add database schema migrations"
        fi
    elif echo "$files" | grep -qE "routes/"; then
        if echo "$diff_content" | grep -qiE "(get|post|put|patch|delete|resource)"; then
            message="define new API routes and endpoints"
        else
            message="update routing configuration"
        fi
    elif echo "$files" | grep -qE "resources/views/"; then
        local view_files=$(echo "$files" | grep -E "resources/views/")
        if echo "$view_files" | grep -qE "\.blade\.php$"; then
            message="update Blade templates and views"
        else
            message="update view templates"
        fi
    elif echo "$files" | grep -qE "resources/js/|resources/css/"; then
        if echo "$files" | grep -qE "\.vue$"; then
            message="update Vue.js components"
        elif echo "$files" | grep -qE "\.js$|\.ts$"; then
            message="update JavaScript functionality"
        elif echo "$files" | grep -qE "\.css$|\.scss$"; then
            message="update stylesheets and UI design"
        else
            message="update frontend assets"
        fi
    elif echo "$files" | grep -qE "config/"; then
        local config_files=$(echo "$files" | grep -E "config/")
        if [[ $(echo "$config_files" | wc -l) -eq 1 ]]; then
            local config_name=$(echo "$config_files" | sed 's/.*\///g' | sed 's/\.php$//g')
            message="configure ${config_name} settings"
        else
            message="update application configuration"
        fi
    elif echo "$files" | grep -qiE "(test|spec)" || echo "$files" | grep -qE "tests/|Tests/"; then
        if echo "$diff_content" | grep -qiE "(test|assert|expect)"; then
            message="add comprehensive test coverage"
        else
            message="update test suite"
        fi
    elif echo "$files" | grep -qE "\.md$|readme|README"; then
        message="update project documentation"
    elif echo "$files" | grep -qE "composer\.(json|lock)"; then
        if echo "$diff_content" | grep -qiE "(require|autoload)"; then
            message="update Composer dependencies"
        else
            message="update package configuration"
        fi
    elif echo "$files" | grep -qE "package\.(json|lock)"; then
        message="update npm dependencies"
    elif echo "$files" | grep -qE "\.env|\.env\."; then
        message="update environment configuration"
    elif echo "$files" | grep -qE "\.github/|\.gitlab-ci|docker|Docker"; then
        message="update CI/CD pipeline configuration"
    elif echo "$files" | grep -qE "webpack|vite|gulpfile|rollup"; then
        message="update build system configuration"
    else
        # Fallback based on file extensions and patterns
        if echo "$files" | grep -qE "\.php$" && [[ "$file_count" -gt 10 ]]; then
            message="major PHP codebase refactoring"
        elif echo "$files" | grep -qE "\.php$"; then
            message="update PHP implementation"
        elif echo "$files" | grep -qE "\.(js|ts|vue)$"; then
            message="update frontend JavaScript code"
        elif echo "$files" | grep -qE "\.(css|scss|sass)$"; then
            message="update stylesheet design"
        else
            message="update project files"
        fi
    fi
    
    # Add file count only if multiple files
    if [[ "$file_count" -gt 1 ]]; then
        message="${message} (${file_count} files)"
    fi
    
    echo "$message"
}

gacp() {
    if ! git rev-parse --git-dir >/dev/null 2>&1; then
        log_error "Not in a git repository"
        return 1
    fi
    
    if ! git add .; then
        log_error "Failed to add files"
        return 1
    fi
    
    local files
    files=$(git diff --cached --name-only 2>/dev/null)
    
    if [[ -z "$files" ]]; then
        echo "[gacp] No staged changes to commit"
        return 0
    fi
    
    local diff_stats diff_content
    diff_stats=$(git diff --cached --numstat 2>/dev/null)
    diff_content=$(git diff --cached 2>/dev/null)
    
    local auto_prefix
    auto_prefix=$(determine_commit_type "$files" "$diff_stats" "$diff_content")
    
    local file_count
    file_count=$(echo "$files" | wc -l | tr -d ' ')
    local preview_files
    preview_files=$(echo "$files" | head -3 | tr '\n' ' ')
    [[ "$file_count" -gt 3 ]] && preview_files="${preview_files}..."
    
    echo "[gacp] Detected changes in $file_count file(s): $preview_files"
    
    local intelligent_message
    intelligent_message=$(generate_intelligent_message "$files" "$diff_stats" "$diff_content" "$auto_prefix" "$file_count")
    
    echo -n "${auto_prefix}: ${intelligent_message}"
    echo -n " > "
    read -r user_input
    
    local final_message
    if [[ -z "$user_input" ]]; then
        final_message="${auto_prefix}: ${intelligent_message}"
    elif is_conventional_commit "$user_input"; then
        final_message="$user_input"
    else
        final_message="${auto_prefix}: ${user_input}"
    fi
    
    if ! git commit -m "$final_message"; then
        log_error "Failed to commit changes"
        return 1
    fi
    
    if ! git remote >/dev/null 2>&1; then
        log_warning "No remote repository configured"
        echo "[gacp] Committed locally: $final_message"
        return 0
    fi
    
    local current_branch
    current_branch=$(git branch --show-current 2>/dev/null)
    if [[ -z "$current_branch" ]]; then
        log_warning "Could not determine current branch"
        echo "[gacp] Committed locally: $final_message"
        return 0
    fi
    
    if ! git rev-parse --abbrev-ref --symbolic-full-name @{u} >/dev/null 2>&1; then
        echo "[gacp] Setting upstream for branch: $current_branch"
        if ! git push -u origin "$current_branch"; then
            log_error "Failed to push and set upstream"
            return 1
        fi
    else
        if ! git push; then
            log_error "Failed to push changes"
            return 1
        fi
    fi
    
    log_success "Committed and pushed: $final_message"
}

determine_commit_type() {
    local files="$1"
    local diff_stats="$2"
    local diff_content="$3"
    local type="chore"
    
    local additions deletions
    additions=$(echo "$diff_stats" | awk '{sum+=$1} END {print sum+0}')
    deletions=$(echo "$diff_stats" | awk '{sum+=$2} END {print sum+0}')
    
    if echo "$files" | grep -qiE "(fix|bug|patch|hotfix)" || \
       echo "$diff_content" | grep -qiE "(fix|bug|error|issue|exception)"; then
        type="fix"
    elif echo "$files" | grep -qiE "(filament|Filament)" || \
         echo "$files" | grep -qE "/Filament/|filament/|resources/views/filament/"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qE "/Models?/|Model\.php$|models?/"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qE "/Controllers?/|Controller\.php$|controllers?/"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qE "/Services?/|Service\.php$|services?/"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qE "/Providers?/|Provider\.php$|providers?/"; then
        type="chore"
    elif echo "$files" | grep -qE "/Middleware/|Middleware\.php$|middleware/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Requests?/|Request\.php$|requests?/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Commands?/|Command\.php$|commands?/|Console/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Jobs?/|Job\.php$|jobs?/|Queue/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Events?/|Event\.php$|events?/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Listeners?/|Listener\.php$|listeners?/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Observers?/|Observer\.php$|observers?/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Policies/|Policy\.php$|policies/"; then
        type="feat"
    elif echo "$files" | grep -qE "/Repositories/|Repository\.php$|repositories/"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qiE "/DTOs?/|DTO\.php$|dtos?/|/Data/|DataTransfer"; then
        type="feat"
    elif echo "$files" | grep -qE "database/migrations/|migrations/"; then
        type="feat"
    elif echo "$files" | grep -qE "database/seeders/|database/factories/|seeders/|factories/"; then
        type="chore"
    elif echo "$files" | grep -qE "routes/|Routes/"; then
        type="feat"
    elif echo "$files" | grep -qiE "(test|spec|Test\.php|\.test\.|\.spec\.)" || \
         echo "$files" | grep -qE "tests/|Tests/|__tests__/|spec/"; then
        type="test"
    elif echo "$files" | grep -qiE "\.(md|txt|rst)$|readme|doc|changelog"; then
        type="docs"
    elif echo "$files" | grep -qiE "\.github/|\.gitlab-ci|jenkins|docker|Docker|\.ci/|ci\.yml|pipeline"; then
        type="ci"
    elif echo "$files" | grep -qE "composer\.(json|lock)|package\.(json|lock)|webpack\.mix\.js|vite\.config\.js|gulpfile|gruntfile|rollup\.config|tsconfig\.json"; then
        type="build"
    elif echo "$files" | grep -qE "config/|Config/|\.env|\.env\."; then
        type="chore"
    elif echo "$diff_content" | grep -qiE "(cache|optimize|performance|speed|query|perf)"; then
        type="perf"
    elif echo "$files" | grep -qE "resources/|Resources/"; then
        if echo "$files" | grep -qE "\.(css|scss|sass|less|styl)$"; then
            type="style"
        else
            type="feat"
        fi
    elif echo "$files" | grep -qE "public/|Public/|assets/|Assets/|static/"; then
        type="chore"
    elif echo "$files" | grep -qE "\.php$"; then
        if [[ "$additions" -gt $((deletions * 2)) ]]; then
            type="feat"
        else
            type="refactor"
        fi
    elif echo "$files" | grep -qE "\.(js|ts|vue|jsx|tsx|svelte)$"; then
        type="feat"
    elif echo "$files" | grep -qE "\.(css|scss|sass|less|styl)$"; then
        type="style"
    fi
    
    echo "$type"
}

if [[ -n "${BASH_VERSION:-}" ]]; then
    :
elif [[ -n "${ZSH_VERSION:-}" ]]; then
    :
else
    log_warning "Shell compatibility: This script is optimized for bash/zsh"
fi