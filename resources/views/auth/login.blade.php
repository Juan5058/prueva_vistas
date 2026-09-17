<x-layouts.guest>
@section('title', 'Ingreso TRD')
<div style="height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2.75rem 1rem 1rem 1rem; box-sizing: border-box; overflow: hidden;">

    {{-- Tarjeta principal glassmorphism compacta --}}
    <div style="width: 100%; max-width: 390px;
                background: rgba(15, 23, 42, 0.45);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 1.25rem;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255, 255, 255, 0.2);
                overflow: visible; position: relative;">

        {{-- Logo circular solapado en la parte superior --}}
        <div style="display: flex; justify-content: center; margin-top: -42px; margin-bottom: 0;">
            <div style="width: 84px; height: 84px; border-radius: 50%; background: #ffffff;
                        border: 3px solid rgba(255, 255, 255, 0.5);
                        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
                        display: flex; align-items: center; justify-content: center;
                        overflow: hidden; flex-shrink: 0;">
                <img src="/images/logo-trd.jpg" alt="Logo TRD"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
        </div>

        {{-- Contenido interno de la tarjeta --}}
        <div style="padding: 0.65rem 1.5rem 1.25rem 1.5rem;">

            {{-- Título --}}
            <div style="text-align: center; margin-bottom: 1rem;">
                <h1 style="font-size: 1.15rem; font-weight: 800; color: #ffffff; margin: 0 0 2px 0; letter-spacing: -0.02em; text-shadow: 0 2px 4px rgba(0,0,0,0.6);">
                    Sistema de Gestión TRD
                </h1>
                <p style="font-size: 0.68rem; color: #cbd5e1; margin: 0; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                    Tablas de Retención Documental · Laravel 13
                </p>
            </div>

            @if(session('security_alert'))
                <div style="margin-bottom: 0.75rem; padding: 0.6rem; border-radius: 0.6rem; background: rgba(244, 63, 94, 0.25); border: 1px solid #f43f5e; text-align: center; backdrop-filter: blur(8px);">
                    <h2 style="font-size: 0.75rem; font-weight: 700; color: #fecdd3; margin: 0 0 0.25rem 0;">
                        {{ session('security_alert.code') === 'IP_NOT_AUTHORIZED' ? 'ALERTA: IP NO AUTORIZADA' : 'SESIÓN FINALIZADA' }}
                    </h2>
                    <p style="font-size: 0.65rem; color: #ffe4e6; font-family: monospace; margin: 0;">{{ session('security_alert.message') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div style="margin-bottom: 0.75rem; padding: 0.5rem; border-radius: 0.6rem; background: rgba(244, 63, 94, 0.25); border: 1px solid #f43f5e; color: #fecdd3; font-size: 0.68rem; backdrop-filter: blur(8px);">
                    <span style="font-weight: 700; display: block;">Acceso Denegado</span>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ route('login.store') }}" id="login-form"
                  style="display: flex; flex-direction: column; gap: 0.65rem;">
                @csrf

                {{-- Email --}}
                <div>
                    <label style="display: block; font-size: 0.6rem; font-weight: 700; color: #e2e8f0; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 0.25rem; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                        Correo Electrónico
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; align-items: center;">
                            <x-icon name="mail" class="w-4 h-4" />
                        </div>
                        <input id="login-input-email" name="email" type="email" required
                               value="{{ old('email', 'admin@trd.gob') }}"
                               style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem;
                                      background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.25);
                                      border-radius: 0.65rem; color: #ffffff; font-size: 0.75rem;
                                      box-sizing: border-box; outline: none; font-family: inherit;
                                      transition: all 0.2s;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.background='rgba(15, 23, 42, 0.8)'; this.style.boxShadow='0 0 10px rgba(59, 130, 246, 0.5)';"
                               onblur="this.style.borderColor='rgba(255, 255, 255, 0.25)'; this.style.background='rgba(15, 23, 42, 0.5)'; this.style.boxShadow='none';">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label style="display: block; font-size: 0.6rem; font-weight: 700; color: #e2e8f0; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 0.25rem; text-shadow: 0 1px 2px rgba(0,0,0,0.5);">
                        Contraseña
                    </label>
                    <div style="position: relative;">
                        <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; align-items: center;">
                            <x-icon name="lock" class="w-4 h-4" />
                        </div>
                        <input id="login-input-password" name="password" type="password" required
                               style="width: 100%; padding: 0.5rem 0.75rem 0.5rem 2.25rem;
                                      background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.25);
                                      border-radius: 0.65rem; color: #ffffff; font-size: 0.75rem;
                                      box-sizing: border-box; outline: none; font-family: inherit;
                                      transition: all 0.2s;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.background='rgba(15, 23, 42, 0.8)'; this.style.boxShadow='0 0 10px rgba(59, 130, 246, 0.5)';"
                               onblur="this.style.borderColor='rgba(255, 255, 255, 0.25)'; this.style.background='rgba(15, 23, 42, 0.5)'; this.style.boxShadow='none';">
                    </div>
                </div>

                {{-- IP --}}
                <div style="padding: 0.35rem 0.65rem; border-radius: 0.5rem; background: rgba(15, 23, 42, 0.45); border: 1px solid rgba(255, 255, 255, 0.15); font-size: 0.62rem; color: #cbd5e1; display: flex; align-items: center; justify-content: space-between; font-family: monospace;">
                    <span>IP Solicitante:</span>
                    <span style="color: #60a5fa; font-weight: 700;">{{ $currentIp ?? '127.0.0.1' }}</span>
                </div>

                {{-- Botón --}}
                <button type="submit"
                        style="width: 100%; padding: 0.65rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: white;
                               font-size: 0.75rem; font-weight: 700; border-radius: 0.65rem;
                               border: 1px solid rgba(255, 255, 255, 0.2); cursor: pointer; letter-spacing: 0.05em;
                               text-transform: uppercase; transition: all 0.2s;
                               box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
                               font-family: inherit; margin-top: 0.15rem;"
                        onmouseover="this.style.background='linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%)'; this.style.boxShadow='0 6px 20px rgba(37, 99, 235, 0.6)';"
                        onmouseout="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'; this.style.boxShadow='0 4px 15px rgba(37, 99, 235, 0.4)';">
                    Ingresar al Sistema TRD
                </button>
            </form>

            {{-- Separador --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; margin: 0.75rem 0 0.5rem 0;">
                <div style="flex: 1; height: 1px; background: rgba(255, 255, 255, 0.18);"></div>
                <span style="font-size: 0.62rem; color: #cbd5e1; white-space: nowrap;">Accesos rápidos</span>
                <div style="flex: 1; height: 1px; background: rgba(255, 255, 255, 0.18);"></div>
            </div>

            {{-- Cuentas RBAC --}}
            <div style="background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 0.65rem; padding: 0.5rem 0.65rem;">
                <p style="font-size: 0.58rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #cbd5e1; margin: 0 0 0.35rem 0;">
                    Cuentas de demostración (RBAC)
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    @foreach([
                        ['admin@trd.gob', 'admin123', 'Super Admin', 'Todos los permisos', '#60a5fa'],
                        ['lider@trd.gob', 'lider123', 'Líder Ambiental', 'Gestión TRD y documentos', '#34d399'],
                        ['aprendiz@trd.gob', 'aprendiz123', 'Aprendiz', 'Consulta y bitácoras', '#fbbf24'],
                    ] as [$email, $pass, $label, $hint, $color])
                        <button type="button" class="quick-login"
                                data-email="{{ $email }}" data-password="{{ $pass }}"
                                style="width: 100%; text-align: left; padding: 0.35rem 0.45rem; border-radius: 0.45rem;
                                       background: transparent; border: 1px solid transparent;
                                       font-size: 0.68rem; display: flex; align-items: center;
                                       justify-content: space-between; cursor: pointer; color: inherit; font-family: inherit; transition: background 0.15s;"
                                onmouseover="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.2)';"
                                onmouseout="this.style.background='transparent'; this.style.borderColor='transparent';">
                            <div>
                                <span style="color: #ffffff; font-weight: 600;">{{ $label }}</span>
                                <span style="color: #cbd5e1; font-size: 0.58rem; margin-left: 0.35rem;">{{ $hint }}</span>
                            </div>
                            <span style="font-size: 0.6rem; font-family: monospace; color: {{ $color }}; font-weight: 700; background: {{ $color }}25; padding: 0.08rem 0.35rem; border-radius: 0.25rem;">{{ $pass }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <p style="margin-top: 0.5rem; text-align: center; font-size: 0.6rem; color: #cbd5e1; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                <x-icon name="shield" class="w-3 h-3 text-emerald-400" />
                <span>Acceso protegido · VerifyUserSessionAndIp</span>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.quick-login').forEach((button) => {
            button.addEventListener('click', () => {
                const email = document.getElementById('login-input-email');
                const password = document.getElementById('login-input-password');
                if (email && password) {
                    email.value = button.dataset.email;
                    password.value = button.dataset.password;
                    document.getElementById('login-form')?.submit();
                }
            });
        });
    });
</script>
</x-layouts.guest>
