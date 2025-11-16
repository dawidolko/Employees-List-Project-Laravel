#!/usr/bin/env bash

#===============================================================================
# Employee Management System - Automatyczny Skrypt Instalacyjny
#===============================================================================
# Ten skrypt automatycznie konfiguruje całe środowisko Laravel wraz z bazą 
# danych employees. Obsługuje instalację zależności, konfigurację bazy danych,
# import pliku SQL i uruchomienie serwera deweloperskiego.
#===============================================================================

set -e  # Zatrzymaj skrypt w przypadku błędu

# Kolory dla outputu
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funkcje pomocnicze
print_header() {
    echo -e "\n${BLUE}========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

# Sprawdzenie czy skrypt jest uruchamiany z właściwego katalogu
check_directory() {
    if [ ! -f "artisan" ] || [ ! -f "composer.json" ]; then
        print_error "Ten skrypt musi być uruchomiony z głównego katalogu projektu Laravel!"
        exit 1
    fi
}

# Sprawdzenie wymaganych narzędzi
check_requirements() {
    print_header "Sprawdzanie wymagań systemowych"
    
    local missing_tools=()
    
    # Sprawdź PHP
    if ! command -v php &> /dev/null; then
        missing_tools+=("PHP")
    else
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        print_success "PHP $PHP_VERSION zainstalowane"
    fi
    
    # Sprawdź Composer
    if ! command -v composer &> /dev/null; then
        missing_tools+=("Composer")
    else
        print_success "Composer zainstalowany"
    fi
    
    # Sprawdź MySQL
    if ! command -v mysql &> /dev/null; then
        missing_tools+=("MySQL")
    else
        print_success "MySQL zainstalowany"
    fi
    
    if [ ${#missing_tools[@]} -ne 0 ]; then
        print_error "Brakujące narzędzia: ${missing_tools[*]}"
        print_info "Zainstaluj brakujące narzędzia i uruchom skrypt ponownie."
        exit 1
    fi
}

# Znalezienie lokalizacji MySQL na macOS
find_mysql_path() {
    # Możliwe lokalizacje MySQL na macOS
    local mysql_paths=(
        "/opt/homebrew/opt/mysql/bin"
        "/usr/local/opt/mysql/bin"
        "/usr/local/mysql/bin"
        "/opt/local/lib/mysql*/bin"
    )
    
    for path in "${mysql_paths[@]}"; do
        if [ -x "$path/mysql" ]; then
            echo "$path"
            return 0
        fi
    done
    
    # Jeśli nie znaleziono w standardowych lokalizacjach, użyj systemowego
    if command -v mysql &> /dev/null; then
        dirname "$(which mysql)"
        return 0
    fi
    
    return 1
}

# Sprawdzenie czy MySQL działa
check_mysql_running() {
    print_header "Sprawdzanie stanu MySQL"
    
    MYSQL_PATH=$(find_mysql_path)
    
    if [ -z "$MYSQL_PATH" ]; then
        print_error "Nie można znaleźć instalacji MySQL"
        exit 1
    fi
    
    print_info "Używam MySQL z: $MYSQL_PATH"
    
    # Sprawdź czy MySQL działa
    if pgrep -x mysqld > /dev/null; then
        print_success "MySQL już działa"
        return 0
    else
        print_warning "MySQL nie działa, próbuję uruchomić..."
        
        # Próba uruchomienia MySQL
        if [ -x "$MYSQL_PATH/../support-files/mysql.server" ]; then
            "$MYSQL_PATH/../support-files/mysql.server" start
        elif [ -x "/opt/homebrew/opt/mysql/bin/mysql.server" ]; then
            /opt/homebrew/opt/mysql/bin/mysql.server start
        elif [ -x "/usr/local/opt/mysql/bin/mysql.server" ]; then
            /usr/local/opt/mysql/bin/mysql.server start
        else
            print_error "Nie można uruchomić MySQL automatycznie"
            print_info "Uruchom MySQL ręcznie i spróbuj ponownie"
            exit 1
        fi
        
        sleep 3
        
        if pgrep -x mysqld > /dev/null; then
            print_success "MySQL uruchomiony pomyślnie"
        else
            print_error "Nie udało się uruchomić MySQL"
            exit 1
        fi
    fi
}

# Konfiguracja połączenia MySQL
configure_mysql_connection() {
    print_header "Konfiguracja połączenia z bazą danych"
    
    # Domyślne wartości
    DB_HOST="127.0.0.1"
    DB_PORT="3306"
    DB_USERNAME="root"
    DB_DATABASE="employees"
    
    # Sprawdzenie czy można połączyć się z MySQL
    print_info "Sprawdzanie połączenia z MySQL..."
    
    if [ -z "$DB_PASSWORD" ]; then
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME"
    else
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME -p$DB_PASSWORD"
    fi
    
    if $MYSQL_CMD -e "SELECT 1;" 2>/dev/null; then
        print_success "Połączenie z MySQL udane"
        return 0
    else
        print_error "Nie można połączyć się z MySQL. Sprawdź hasło."
        print_info "Spróbuj ręcznie: mysql -u root -p"
        exit 1
    fi
}

# Tworzenie bazy danych
create_database() {
    print_header "Tworzenie bazy danych"
    
    print_info "Sprawdzanie czy baza '$DB_DATABASE' istnieje..."
    
    if [ -z "$DB_PASSWORD" ]; then
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME"
    else
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME -p$DB_PASSWORD"
    fi
    
    DB_EXISTS=$($MYSQL_CMD -sse "SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$DB_DATABASE';" 2>/dev/null)
    
    if [ "$DB_EXISTS" -eq 1 ]; then
        print_warning "Baza danych '$DB_DATABASE' już istnieje"
        read -p "Czy chcesz ją usunąć i utworzyć na nowo? (t/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Tt]$ ]]; then
            $MYSQL_CMD -e "DROP DATABASE IF EXISTS $DB_DATABASE;" 2>/dev/null
            print_success "Stara baza danych usunięta"
        else
            print_info "Używam istniejącej bazy danych"
            return 0
        fi
    fi
    
    $MYSQL_CMD -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    print_success "Baza danych '$DB_DATABASE' utworzona"
}

# Klonowanie repozytorium test_db employees
clone_test_db() {
    print_header "Pobieranie bazy danych employees (test_db)"
    
    TESTDB_DIR="test_db"
    
    # Sprawdź czy katalog już istnieje
    if [ -d "$TESTDB_DIR" ]; then
        print_info "Katalog test_db już istnieje"
        
        if [ -f "$TESTDB_DIR/employees.sql" ]; then
            print_success "Plik employees.sql już istnieje"
            return 0
        else
            print_warning "Katalog istnieje, ale brakuje employees.sql"
            rm -rf "$TESTDB_DIR"
        fi
    fi
    
    # Sprawdź czy git jest zainstalowany
    if ! command -v git &> /dev/null; then
        print_error "Git nie jest zainstalowany!"
        print_info "Zainstaluj git lub pobierz bazę ręcznie z: https://github.com/datacharmer/test_db"
        exit 1
    fi
    
    print_info "Klonowanie repozytorium test_db z GitHub..."
    
    if git clone https://github.com/datacharmer/test_db.git "$TESTDB_DIR"; then
        print_success "Repozytorium test_db pobrane pomyślnie"
    else
        print_error "Błąd podczas klonowania repozytorium"
        exit 1
    fi
    
    if [ ! -f "$TESTDB_DIR/employees.sql" ]; then
        print_error "Plik employees.sql nie został znaleziony w sklonowanym repozytorium"
        exit 1
    fi
}

# Import pliku SQL z danymi pracowników
import_employees_data() {
    print_header "Import danych pracowników"
    
    # Szukanie pliku employees.sql
    SQL_FILE=""
    
    # Najpierw sprawdź w sklonowanym katalogu test_db
    if [ -f "test_db/employees.sql" ]; then
        SQL_FILE="test_db/employees.sql"
    elif [ -f "employees.sql" ]; then
        SQL_FILE="employees.sql"
    elif [ -f "database/employees.sql" ]; then
        SQL_FILE="database/employees.sql"
    elif [ -f "../employees.sql" ]; then
        SQL_FILE="../employees.sql"
    elif [ -f "$HOME/Desktop/employees.sql" ]; then
        SQL_FILE="$HOME/Desktop/employees.sql"
    elif [ -f "$HOME/Downloads/employees.sql" ]; then
        SQL_FILE="$HOME/Downloads/employees.sql"
    else
        print_error "Nie znaleziono pliku employees.sql!"
        print_info "Sprawdź czy folder test_db został poprawnie sklonowany"
        exit 1
    fi
    
    print_info "Importowanie danych z pliku: $SQL_FILE"
    print_warning "To może potrwać kilka minut (około 300,000 rekordów)..."
    
    SQL_DIR=$(dirname "$SQL_FILE")
    ORIGINAL_DIR=$(pwd)
    
    if [ -z "$DB_PASSWORD" ]; then
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME"
    else
        MYSQL_CMD="mysql -h$DB_HOST -P$DB_PORT -u$DB_USERNAME -p$DB_PASSWORD"
    fi
    
    # Przejdź do katalogu test_db (tam są pliki .dump)
    cd "$SQL_DIR" || {
        print_error "Nie można zmienić katalogu na $SQL_DIR"
        exit 1
    }
    
    # Załaduj strukturę bazy (bez komend SOURCE)
    print_info "Tworzenie struktury bazy danych..."
    cat employees.sql | grep -v "^source" | $MYSQL_CMD $DB_DATABASE 2>&1 | grep -v "Warning" | grep "INFO"
    
    # Załaduj dane ręcznie z plików .dump
    print_info "Ładowanie działów..."
    $MYSQL_CMD $DB_DATABASE < load_departments.dump 2>/dev/null
    
    print_info "Ładowanie pracowników..."
    $MYSQL_CMD $DB_DATABASE < load_employees.dump 2>/dev/null
    
    print_info "Ładowanie przypisań działów..."
    $MYSQL_CMD $DB_DATABASE < load_dept_emp.dump 2>/dev/null
    
    print_info "Ładowanie menedżerów..."
    $MYSQL_CMD $DB_DATABASE < load_dept_manager.dump 2>/dev/null
    
    print_info "Ładowanie stanowisk..."
    $MYSQL_CMD $DB_DATABASE < load_titles.dump 2>/dev/null
    
    print_info "Ładowanie wynagrodzeń (część 1/3)..."
    $MYSQL_CMD $DB_DATABASE < load_salaries1.dump 2>/dev/null
    
    print_info "Ładowanie wynagrodzeń (część 2/3)..."
    $MYSQL_CMD $DB_DATABASE < load_salaries2.dump 2>/dev/null
    
    print_info "Ładowanie wynagrodzeń (część 3/3)..."
    $MYSQL_CMD $DB_DATABASE < load_salaries3.dump 2>/dev/null
    
    cd "$ORIGINAL_DIR"
    
    # Sprawdzenie czy dane zostały zaimportowane
    TABLES_COUNT=$($MYSQL_CMD -sse "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$DB_DATABASE';" 2>/dev/null)
    
    if [ "$TABLES_COUNT" -gt 0 ]; then
        print_success "Dane pracowników zaimportowane pomyślnie"
        print_info "Zaimportowano $TABLES_COUNT tabel"
        
        # Sprawdź liczbę pracowników
        EMPLOYEES_COUNT=$($MYSQL_CMD -sse "SELECT COUNT(*) FROM $DB_DATABASE.employees;" 2>/dev/null || echo "0")
        print_info "Liczba pracowników w bazie: $EMPLOYEES_COUNT"
    else
        print_error "Import nie powiódł się - brak tabel w bazie"
        cd "$ORIGINAL_DIR"
        exit 1
    fi
}

# Test instalacji bazy employees
test_employees_installation() {
    print_header "Testowanie instalacji bazy danych"
    
    TESTDB_DIR="test_db"
    TEST_FILE="$TESTDB_DIR/test_employees_md5.sql"
    
    if [ ! -f "$TEST_FILE" ]; then
        print_warning "Plik testowy nie został znaleziony, pomijam testy"
        return 0
    fi
    
    print_info "Uruchamiam test MD5..."
    
    if mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" ${DB_PASSWORD:+-p"$DB_PASSWORD"} -t "$DB_DATABASE" < "$TEST_FILE"; then
        print_success "Testy zakończone - sprawdź wyniki powyżej"
    else
        print_warning "Test zakończony z błędami"
    fi
}

# Konfiguracja pliku .env
setup_env_file() {
    print_header "Konfiguracja pliku środowiskowego (.env)"
    
    if [ -f ".env" ]; then
        print_warning "Plik .env już istnieje"
        read -p "Czy chcesz go nadpisać? (t/n): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Tt]$ ]]; then
            print_info "Zachowuję istniejący plik .env"
            return 0
        fi
    fi
    
    if [ ! -f ".env.example" ]; then
        print_error "Plik .env.example nie istnieje!"
        exit 1
    fi
    
    cp .env.example .env
    print_success "Plik .env utworzony z .env.example"
    
    # Aktualizacja konfiguracji bazy danych w .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$DB_DATABASE/" .env
    sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$DB_USERNAME/" .env
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASSWORD/" .env
    rm .env.bak
    
    print_success "Konfiguracja bazy danych zaktualizowana w .env"
}

# Instalacja zależności PHP
install_dependencies() {
    print_header "Instalacja zależności PHP (Composer)"
    
    if [ ! -f "composer.json" ]; then
        print_error "Plik composer.json nie istnieje!"
        exit 1
    fi
    
    print_info "Instalowanie pakietów Composer..."
    
    # PHP 8.4 wymaga aktualizacji zależności
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;" 2>/dev/null)
    if [[ "$PHP_VERSION" == "8.4" ]]; then
        print_warning "PHP 8.4 wykryte - resetuję composer.lock i vendor..."
        
        # USUŃ composer.lock i vendor PRZED updatem
        rm -f composer.lock
        rm -rf vendor
        
        print_info "Aktualizacja wszystkich zależności dla PHP 8.4..."
        print_info "To zajmie 3-5 minut, proszę czekać..."
        
        if composer update --no-interaction --prefer-dist --optimize-autoloader --with-all-dependencies 2>&1 | grep -E "Package operations|Generating|Nothing to" | tail -5; then
            print_success "Zależności PHP zaktualizowane dla PHP 8.4"
        else
            print_error "Błąd podczas aktualizacji zależności PHP"
            exit 1
        fi
    else
        if composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | grep -v "Deprecation Notice" | tail -10; then
            print_success "Zależności PHP zainstalowane"
        else
            print_error "Błąd podczas instalacji zależności PHP"
            exit 1
        fi
    fi
    
    # Sprawdzenie czy vendor/autoload.php istnieje
    if [ ! -f "vendor/autoload.php" ]; then
        print_error "Plik vendor/autoload.php nie został utworzony!"
        exit 1
    fi
    
    print_success "vendor/autoload.php gotowy"
    
    # Sprawdzenie czy wymagane pakiety są zainstalowane
    print_info "Sprawdzanie wymaganych pakietów..."
    
    REQUIRED_PACKAGES=("barryvdh/laravel-dompdf" "spatie/laravel-query-builder" "livewire/livewire")
    
    for package in "${REQUIRED_PACKAGES[@]}"; do
        if composer show "$package" &> /dev/null; then
            print_success "Pakiet $package zainstalowany"
        else
            print_warning "Pakiet $package nie jest zainstalowany"
            # Nie instaluj automatycznie - composer update powinien to załatwić
        fi
    done
}

# Instalacja zależności Node.js (jeśli istnieje package.json)
install_node_dependencies() {
    if [ -f "package.json" ]; then
        print_header "Instalacja zależności Node.js"
        
        if command -v npm &> /dev/null; then
            print_info "Instalowanie pakietów npm..."
            npm install
            print_success "Zależności Node.js zainstalowane"
        else
            print_warning "npm nie jest zainstalowany, pomijam instalację zależności Node.js"
        fi
    fi
}

# Generowanie klucza aplikacji
generate_app_key() {
    print_header "Generowanie klucza aplikacji"
    
    if php artisan key:generate --force 2>&1 | grep -v "Warning" | grep -i "INFO" || true; then
        print_success "Klucz aplikacji wygenerowany"
    else
        print_error "Błąd podczas generowania klucza aplikacji"
        exit 1
    fi
}

# Tworzenie linku symbolicznego do storage
create_storage_link() {
    print_header "Konfiguracja storage"
    
    if [ -L "public/storage" ]; then
        print_info "Link symboliczny do storage już istnieje"
    else
        if php artisan storage:link 2>&1 | grep -v "Warning" | grep -i "INFO" || true; then
            print_success "Link symboliczny do storage utworzony"
        else
            print_warning "Nie udało się utworzyć linku symbolicznego do storage"
        fi
    fi
}

# Czyszczenie i optymalizacja cache
optimize_application() {
    print_header "Optymalizacja aplikacji"
    
    print_info "Czyszczenie cache..."
    php artisan config:clear 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    php artisan cache:clear 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    php artisan view:clear 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    php artisan route:clear 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    
    print_info "Cachowanie konfiguracji..."
    php artisan config:cache 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    php artisan route:cache 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    php artisan view:cache 2>&1 | grep -v "Warning" | grep -i "INFO" || true
    
    print_success "Aplikacja zoptymalizowana"
}

# Sprawdzenie uprawnień do katalogów
check_permissions() {
    print_header "Sprawdzanie uprawnień katalogów"
    
    DIRS_TO_CHECK=("storage" "bootstrap/cache")
    
    for dir in "${DIRS_TO_CHECK[@]}"; do
        if [ -d "$dir" ]; then
            chmod -R 775 "$dir"
            print_success "Uprawnienia dla $dir ustawione"
        else
            print_warning "Katalog $dir nie istnieje"
        fi
    done
}

# Uruchomienie serwera deweloperskiego
start_server() {
    print_header "Uruchamianie serwera deweloperskiego"
    
    # Sprawdzenie czy port 8000 jest wolny
    if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
        print_warning "Port 8000 jest już zajęty"
        read -p "Czy chcesz zabić proces zajmujący port 8000? (t/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Tt]$ ]]; then
            lsof -ti:8000 | xargs kill -9
            print_success "Proces na porcie 8000 zakończony"
        else
            print_info "Wybierz inny port lub zakończ proces ręcznie"
            return 0
        fi
    fi
    
    print_success "Wszystko gotowe!"
    echo
    print_info "Aplikacja będzie dostępna pod adresem: ${GREEN}http://localhost:8000${NC}"
    echo
    print_info "Uruchamiam serwer deweloperski..."
    print_warning "Naciśnij Ctrl+C aby zatrzymać serwer"
    echo
    
    # Uruchomienie serwera
    php artisan serve
}

# Wyświetlenie podsumowania
show_summary() {
    print_header "Podsumowanie instalacji"
    
    echo -e "${GREEN}✓ Projekt Laravel skonfigurowany${NC}"
    echo -e "${GREEN}✓ Baza danych '$DB_DATABASE' gotowa${NC}"
    echo -e "${GREEN}✓ Dane pracowników zaimportowane${NC}"
    echo -e "${GREEN}✓ Wszystkie zależności zainstalowane${NC}"
    echo
    echo -e "${BLUE}Aby uruchomić serwer ponownie, użyj:${NC}"
    echo -e "  ${YELLOW}php artisan serve${NC}"
    echo
    echo -e "${BLUE}Aplikacja będzie dostępna pod:${NC}"
    echo -e "  ${GREEN}http://localhost:8000${NC}"
    echo
}

#===============================================================================
# GŁÓWNA LOGIKA SKRYPTU
#===============================================================================

main() {
    clear
    
    echo -e "${BLUE}"
    echo "╔═══════════════════════════════════════════════════════════╗"
    echo "║   Employee Management System - Instalator Automatyczny   ║"
    echo "╚═══════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
    
    # Pobierz hasło MySQL na początku
    print_info "Skrypt będzie potrzebował dostępu do MySQL"
    echo -n "Podaj hasło MySQL dla użytkownika root (naciśnij Enter jeśli brak hasła): "
    read -s MYSQL_PASSWORD_INPUT
    echo
    export DB_PASSWORD="$MYSQL_PASSWORD_INPUT"
    
    # Sprawdzenie katalogu
    check_directory
    
    # Sprawdzenie wymagań
    check_requirements
    
    # Sprawdzenie i uruchomienie MySQL
    check_mysql_running
    
    # Konfiguracja połączenia MySQL
    configure_mysql_connection
    
    # Tworzenie bazy danych
    create_database
    
    # Klonowanie repozytorium test_db
    clone_test_db
    
    # Import danych pracowników
    import_employees_data
    
    # Konfiguracja pliku .env
    setup_env_file
    
    # Instalacja zależności
    install_dependencies
    
    # Instalacja zależności Node.js
    install_node_dependencies
    
    # Generowanie klucza aplikacji
    generate_app_key
    
    # Tworzenie linku do storage
    create_storage_link
    
    # Sprawdzenie uprawnień
    check_permissions
    
    # Optymalizacja aplikacji
    optimize_application
    
    # Wyświetlenie podsumowania
    show_summary
    
    # Uruchomienie serwera
    read -p "Czy chcesz uruchomić serwer deweloperski teraz? (t/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Tt]$ ]]; then
        start_server
    else
        print_info "Możesz uruchomić serwer później komendą: php artisan serve"
    fi
}

# Uruchomienie głównej funkcji
main "$@"

php -r "copy('.env.example', '.env');"

composer install

# composer update

composer require laravel/sanctum

composer require barryvdh/laravel-dompdf

php artisan key:generate

php artisan storage:link

# php artisan migrate

# php artisan db:seed

# php artisan migrate:fresh --seed

php artisan serve

code .
