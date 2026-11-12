<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarima extends Model
{
    use HasFactory;

    protected $table = 'tarima';

    protected $fillable = [
        'id_customer',
        'who_register',
        'serial_number',
        'register_date',
        'document_url',
        'active',
        'status',
        'delivered_date',
        'who_delivered',
    ];

    protected $casts = [
        'register_date' => 'datetime',
        'delivered_date' => 'datetime',
        'active' => 'boolean',
    ];

    public const STATUS_INPROCESS = 'inprocess';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_READYTODELIVER = 'readytodeliver';
    public const STATUS_DELIVERED = 'delivered';

    public function deliveredBy(){
        return $this->belongsTo(User::class, 'who_delivered');
    }

    // Todos los procesos de la tarima terminados y cada uno con reporte de medición
    public function meetsDeliveryRule(): bool
    {
        $processes = Proccess::whereHas('tarimaNp', function ($query) {
                $query->where('id_tarima', $this->id);
            })
            ->withCount('meditionsReports')
            ->get();

        return $processes->isNotEmpty()
            && $processes->every(fn ($p) => $p->status === 'finished' && $p->meditions_reports_count > 0);
    }

    // Pasa la tarima confirmada a "listo para entrega" cuando cumple la regla
    public function refreshDeliveryStatus(): void
    {
        if ($this->status === self::STATUS_FINISHED && $this->meetsDeliveryRule()) {
            $this->status = self::STATUS_READYTODELIVER;
            $this->save();
        }
    }

    public function customer(){
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function registeredBy(){
        return $this->belongsTo(User::class, 'who_register');
    }

    public function tarimaNps(){
        return $this->hasMany(TarimaNp::class, 'id_tarima');
    }

    public static function lastRegisterId(): int
    {
        return (int) (static::query()->max('id') ?? 0);
        
    }

    public static function lastSerialNumber(): ?string
    {
        return static::query()->latest('id')->value('serial_number');
    }
}
