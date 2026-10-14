<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_pago', function (Blueprint $table) {
            $table->id();
            $table->string('cod', 20)->unique();
            $table->foreignId('rsv_id')->unique()->constrained('reservas')->restrictOnDelete();
            $table->date('fec_emi')->default(DB::raw('CURRENT_DATE'));
            $table->decimal('mto_tot', 10, 2);
            $table->string('est', 15)->default('emitido');
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cmp_pag_id')->constrained('comprobantes_pago')->cascadeOnDelete();
            $table->string('mtd', 15);
            $table->decimal('mto', 10, 2);
            $table->string('ref_trx', 100)->nullable();
            $table->timestamp('pag_en')->useCurrent();
            $table->string('est', 15)->default('completado');
        });

        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('rsv_id')->unique()->nullable()->constrained('reservas')->setNullOnDelete();
            $table->smallInteger('cal');
            $table->text('com')->nullable();
            $table->jsonb('mdt')->nullable();
            $table->timestamp('cre_en')->useCurrent();
        });

        Schema::create('conversaciones_chatbot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('id_ses', 100);
            $table->string('can', 20)->default('web');
            $table->timestamp('ini_en')->useCurrent();
            $table->timestamp('fin_en')->nullable();
        });

        Schema::create('mensajes_chatbot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cnv_id')->constrained('conversaciones_chatbot')->cascadeOnDelete();
            $table->string('rem', 10);
            $table->text('txt_msj');
            $table->string('int_det', 50)->nullable();
            $table->timestamp('cre_en')->useCurrent();
        });

        Schema::create('bitacora_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom_tbl', 100);
            $table->unsignedBigInteger('id_reg');
            $table->string('acc', 10);
            $table->jsonb('val_ant')->nullable();
            $table->jsonb('val_nvo')->nullable();
            $table->string('dir_ip', 45)->nullable();
            $table->text('age_usr')->nullable();
            $table->timestamp('cre_en')->useCurrent();
        });

        Schema::create('registros_notificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rsv_id')->nullable()->constrained('reservas')->nullOnDelete();
            $table->foreignId('usr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('can', 15);
            $table->string('tip', 50);
            $table->string('est', 15)->default('pendiente');
            $table->jsonb('cnd')->nullable();
            $table->timestamp('env_en')->nullable();
        });

        Schema::create('proyecciones_demanda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->smallInteger('mes_per');
            $table->smallInteger('ani_per');
            $table->integer('rsv_pry');
            $table->string('ver_mdl', 30)->nullable();
            $table->timestamp('gen_en')->useCurrent();
            $table->unique(['tur_id', 'mes_per', 'ani_per']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecciones_demanda');
        Schema::dropIfExists('registros_notificacion');
        Schema::dropIfExists('bitacora_auditoria');
        Schema::dropIfExists('mensajes_chatbot');
        Schema::dropIfExists('conversaciones_chatbot');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('comprobantes_pago');
    }
};
