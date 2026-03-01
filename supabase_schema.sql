-- Supabase (Postgres) schema para Login MVC + OTP + Auditoría
-- Ejecuta esto en Supabase SQL Editor

create extension if not exists pgcrypto;
create extension if not exists citext;

create table if not exists public.users (
  id uuid primary key default gen_random_uuid(),
  name text not null,
  email citext unique not null,
  phone text,
  type text,
  department text,
  position text,
  hired_at date,
  status text,
  photo_url text,
  password_hash text not null,
  must_change_password boolean not null default false,
  created_at timestamptz not null default now()
);

create table if not exists public.otp_codes (
  id bigserial primary key,
  user_id uuid not null references public.users(id) on delete cascade,
  code_hash text not null,
  expires_at timestamptz not null,
  used_at timestamptz,
  attempts int not null default 0,
  ip text,
  user_agent text,
  created_at timestamptz not null default now()
);

create index if not exists idx_otp_user_active
on public.otp_codes (user_id, created_at desc)
where used_at is null;

create table if not exists public.audit_logs (
  id bigserial primary key,
  event text not null,
  email citext not null,
  user_id uuid references public.users(id) on delete set null,
  ip text,
  user_agent text,
  created_at timestamptz not null default now(),
  details text
);

create index if not exists idx_audit_created_at on public.audit_logs (created_at desc);
create index if not exists idx_audit_email_created_at on public.audit_logs (email, created_at desc);
