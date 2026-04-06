# Sistema de Ponto Colaborador (Laravel + Blade + Bootstrap)

Projeto multiempresa para controle de ponto com foco em produto vendavel.

## Funcionalidades implementadas

- Multiempresa com login unico por empresa
- Registro de ponto por colaborador com PIN (entrada e saida)
- Dashboard com:
	- horas trabalhadas
	- atrasos
	- faltas
	- alertas de quem nao bateu ponto hoje
	- alertas de atraso no dia
- Relatorio de espelho de ponto:
	- entrada e saida por dia
	- total de horas
	- horas extras
- Exportacao de relatorio em PDF e Excel
- Fechamento mensal:
	- bloqueia novos lancamentos no mes fechado
- Logs de auditoria:
	- registra quem alterou o que (ex: criacao de ponto e fechamento)

## Arquitetura

O projeto esta organizado em camadas:

- Interfaces: contratos de persistencia em app/Repositories/Contracts
- Repositories: implementacao Eloquent em app/Repositories/Eloquent
- Services: regras de negocio em app/Services
- Controllers: orquestracao HTTP em app/Http/Controllers
- Blade Components: componentes reutilizaveis em resources/views/components

## Banco de dados

Compativel com MySQL (tambem roda em SQLite para dev/test).

Tabelas principais:

- companies
- employees
- time_punches
- monthly_closures
- attendance_adjustments
- audit_logs

## Instalar e rodar

1. Instalar dependencias:

```bash
composer install
npm install
```

2. Configurar ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configurar MySQL no .env e rodar migrations:

```bash
php artisan migrate:fresh --seed
```

4. Iniciar ambiente:

```bash
composer run dev
```

## Credenciais de demo

- Empresa login: empresa.demo
- Senha: 123456
- Colaborador 1 PIN: 1111
- Colaborador 2 PIN: 2222

## Rotas principais

- /login
- /dashboard
- /kiosk
- /reports

## Testes

```bash
php artisan test
```
