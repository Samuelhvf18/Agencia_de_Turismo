<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION auditar_fila_negocio() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE
    antes jsonb;
    despues jsonb;
    actor bigint;
    ocultos text[] := ARRAY['clv','password','remember_token','two_factor_secret','two_factor_recovery_codes','token'];
BEGIN
    IF TG_OP <> 'INSERT' THEN antes := to_jsonb(OLD) - ocultos; END IF;
    IF TG_OP <> 'DELETE' THEN despues := to_jsonb(NEW) - ocultos; END IF;
    SELECT id INTO actor FROM users WHERE id = NULLIF(current_setting('app.usuario', true), '')::bigint;
    INSERT INTO bitacora_auditoria(usr_id, nom_tbl, id_reg, acc, val_ant, val_nvo, dir_ip, age_usr, cre_en)
    VALUES(actor, TG_TABLE_NAME, COALESCE((despues->>'id')::bigint, (antes->>'id')::bigint, 0),
        CASE TG_OP WHEN 'INSERT' THEN 'CREATE' WHEN 'UPDATE' THEN 'UPDATE' ELSE 'DELETE' END,
        antes, despues, NULLIF(current_setting('app.ip', true), ''),
        COALESCE(NULLIF(current_setting('app.agente', true), ''), 'sistema/planificador'), now());
    RETURN COALESCE(NEW, OLD);
END $$;
DO $$ DECLARE tabla record; BEGIN
    FOR tabla IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
        AND tablename NOT IN ('bitacora_auditoria','migrations','sessions','cache','cache_locks','jobs','job_batches','failed_jobs','password_reset_tokens','personal_access_tokens')
    LOOP
        EXECUTE format('CREATE TRIGGER auditoria_negocio AFTER INSERT OR UPDATE OR DELETE ON %I FOR EACH ROW EXECUTE FUNCTION auditar_fila_negocio()', tabla.tablename);
    END LOOP;
END $$;
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS auditar_fila_negocio() CASCADE');
    }
};
