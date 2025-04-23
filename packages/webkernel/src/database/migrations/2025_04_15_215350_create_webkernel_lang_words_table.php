<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('webkernel_lang_words', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lang');                 // FK vers webkernel_lang
            $table->string('lang_ref');                              // ex: 'wki_blog_categories'
            $table->text('translation')->nullable();          // supporte HTML
            $table->string('app')->default('core');             // ex: 'solEcoles', 'crm'
            $table->string('theme')->default('none');           // ex: 'default'
            $table->timestamps();
            $table->unsignedBigInteger('belongs_to')->default(1);

            // 🔐 Clé étrangère sans suppression cascade
            $table->foreign('lang')
                ->references('id')
                ->on('webkernel_lang')
                ->onDelete('restrict');

            // ⚡ Index pour performance
            $table->index(['lang_ref', 'lang']);              // clé+langue : unique par langue
            $table->index(['app', 'theme']);             // app/thème
            $table->index('belongs_to');                 // multi-tenant
            $table->unique(['lang_ref', 'lang', 'app', 'theme']);

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webkernel_lang_words');
    }
};
