<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Modulos del sistema a los que se les puede dar acceso
    public const PERMISOS = [
        'facturas' => 'Facturación',
        'anular_facturas' => 'Anular facturas',
        'clientes' => 'Clientes',
        'productos' => 'Productos y categorías',
        'inventario' => 'Movimientos de inventario',
        'ncf' => 'Comprobantes NCF',
        'reportes' => 'Reportes',
        'empresa' => 'Datos de la empresa',
        'usuarios' => 'Usuarios',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'es_admin',
        'activo',
        'permisos',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'es_admin' => 'boolean',
            'activo' => 'boolean',
            'permisos' => 'array',
        ];
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    // El administrador puede todo, los demas solo lo que tengan marcado
    public function puede($permiso)
    {
        if ($this->es_admin) {
            return true;
        }

        return in_array($permiso, $this->permisos ?? []);
    }
}
