-- Insertar datos de prueba para testing del login
-- Ejecutar después de crear las tablas

-- Insertar roles si no existen
INSERT INTO public.roles (id, name, description) VALUES 
(1, 'ADMIN', 'Administrador del sistema'),
(2, 'USER', 'Usuario regular'),
(3, 'MANAGER', 'Gerente de tienda')
ON CONFLICT (id) DO NOTHING;

-- Reiniciar secuencia de roles
SELECT setval('public.roles_id_seq', (SELECT MAX(id) FROM public.roles));

-- Insertar usuarios de prueba
-- Contraseña para todos: 123456 (hasheada)
INSERT INTO public.users (id, username, email, name, password, status, role_id, created_at) VALUES 
(1, 'admin', 'admin@tienda.com', 'Administrador', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE', 1, now()),
(2, 'carlos', 'carlos@tienda.com', 'Carlos Pérez', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE', 2, now()),
(3, 'manager', 'manager@tienda.com', 'Gerente Tienda', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE', 3, now()),
(4, 'test', 'test@ejemplo.com', 'Usuario Test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ACTIVE', 2, now())
ON CONFLICT (id) DO NOTHING;

-- Reiniciar secuencia de usuarios
SELECT setval('public.users_id_seq', (SELECT MAX(id) FROM public.users));

-- Información de login para testing:
-- Username: admin | Email: admin@tienda.com | Password: 123456
-- Username: carlos | Email: carlos@tienda.com | Password: 123456
-- Username: manager | Email: manager@tienda.com | Password: 123456
-- Username: test | Email: test@ejemplo.com | Password: 123456
