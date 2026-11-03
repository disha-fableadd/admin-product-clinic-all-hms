<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates all tables from SQL database dump.
     * If table already exists, it skips creation and adds any missing columns.
     */
    public function up(): void
    {
        // Table: ai_credentials
        if (!Schema::hasTable('ai_credentials')) {
            Schema::create('ai_credentials', function (Blueprint $table) {
                $table->id();
                $table->string('service_name');
                $table->string('api_key');
                $table->boolean('is_default')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('ai_credentials', function (Blueprint $table) {
                if (!Schema::hasColumn('ai_credentials', 'service_name')) {
                    $table->string('service_name');
                }
                if (!Schema::hasColumn('ai_credentials', 'api_key')) {
                    $table->string('api_key');
                }
                if (!Schema::hasColumn('ai_credentials', 'is_default')) {
                    $table->boolean('is_default')->default(0);
                }
                if (!Schema::hasColumn('ai_credentials', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('ai_credentials', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: appointment_history
        if (!Schema::hasTable('appointment_history')) {
            Schema::create('appointment_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('appointment_id');
                $table->date('date');
                $table->time('time');
                $table->string('status');
                $table->text('comment')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('appointment_history', function (Blueprint $table) {
                if (!Schema::hasColumn('appointment_history', 'appointment_id')) {
                    $table->unsignedBigInteger('appointment_id');
                }
                if (!Schema::hasColumn('appointment_history', 'date')) {
                    $table->date('date');
                }
                if (!Schema::hasColumn('appointment_history', 'time')) {
                    $table->time('time');
                }
                if (!Schema::hasColumn('appointment_history', 'status')) {
                    $table->string('status');
                }
                if (!Schema::hasColumn('appointment_history', 'comment')) {
                    $table->text('comment')->nullable();
                }
                if (!Schema::hasColumn('appointment_history', 'created_by')) {
                    $table->unsignedBigInteger('created_by');
                }
                if (!Schema::hasColumn('appointment_history', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('appointment_history', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: appointments
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('doctor_id');
                $table->unsignedBigInteger('treatment_id');
                $table->enum('appoint_type', ['virtual', 'in-person'])->nullable();
                $table->enum('status', ['upcoming', 'confirmed', 'completed', 'cancelled', 'follow-up'])->nullable();
                $table->date('date')->nullable();
                $table->time('duration')->nullable();
                $table->string('clinic_location')->nullable();
                $table->text('followup_update')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('appointments', function (Blueprint $table) {
                if (!Schema::hasColumn('appointments', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('appointments', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('appointments', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('appointments', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id');
                }
                if (!Schema::hasColumn('appointments', 'appoint_type')) {
                    $table->enum('appoint_type', ['virtual', 'in-person'])->nullable();
                }
                if (!Schema::hasColumn('appointments', 'status')) {
                    $table->enum('status', ['upcoming', 'confirmed', 'completed', 'cancelled', 'follow-up'])->nullable();
                }
                if (!Schema::hasColumn('appointments', 'date')) {
                    $table->date('date')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'duration')) {
                    $table->time('duration')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'clinic_location')) {
                    $table->string('clinic_location')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'followup_update')) {
                    $table->text('followup_update')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('appointments', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: assessment_template
        if (!Schema::hasTable('assessment_template')) {
            Schema::create('assessment_template', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('image')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('assessment_template', function (Blueprint $table) {
                if (!Schema::hasColumn('assessment_template', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'title')) {
                    $table->longText('title')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'image')) {
                    $table->longText('image')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('assessment_template', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: assigned_therapies
        if (!Schema::hasTable('assigned_therapies')) {
            Schema::create('assigned_therapies', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('therapy_id');
                $table->unsignedBigInteger('doctor_id');
                $table->enum('type', ['virtual', 'inperson'])->default('inperson');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('assigned_therapies', function (Blueprint $table) {
                if (!Schema::hasColumn('assigned_therapies', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('assigned_therapies', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('assigned_therapies', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('assigned_therapies', 'therapy_id')) {
                    $table->unsignedBigInteger('therapy_id');
                }
                if (!Schema::hasColumn('assigned_therapies', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('assigned_therapies', 'type')) {
                    $table->enum('type', ['virtual', 'inperson'])->default('inperson');
                }
                if (!Schema::hasColumn('assigned_therapies', 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn('assigned_therapies', 'end_date')) {
                    $table->date('end_date')->nullable();
                }
                if (!Schema::hasColumn('assigned_therapies', 'status')) {
                    $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
                }
                if (!Schema::hasColumn('assigned_therapies', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('assigned_therapies', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: bank_master
        if (!Schema::hasTable('bank_master')) {
            Schema::create('bank_master', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('branch_id')->nullable();
                $table->string('bank_name');
                $table->string('account_number')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('branch_name')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0.00);
                $table->tinyInteger('status')->default(1);
                $table->integer('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('bank_master', function (Blueprint $table) {
                if (!Schema::hasColumn('bank_master', 'branch_id')) {
                    $table->bigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('bank_master', 'bank_name')) {
                    $table->string('bank_name');
                }
                if (!Schema::hasColumn('bank_master', 'account_number')) {
                    $table->string('account_number')->nullable();
                }
                if (!Schema::hasColumn('bank_master', 'ifsc_code')) {
                    $table->string('ifsc_code')->nullable();
                }
                if (!Schema::hasColumn('bank_master', 'branch_name')) {
                    $table->string('branch_name')->nullable();
                }
                if (!Schema::hasColumn('bank_master', 'opening_balance')) {
                    $table->decimal('opening_balance', 15, 2)->default(0.00);
                }
                if (!Schema::hasColumn('bank_master', 'status')) {
                    $table->tinyInteger('status')->default(1);
                }
                if (!Schema::hasColumn('bank_master', 'isDeleted')) {
                    $table->integer('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('bank_master', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('bank_master', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: branches
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['email'], 'branches_email_unique');
            });
        } else {
            Schema::table('branches', function (Blueprint $table) {
                if (!Schema::hasColumn('branches', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('branches', 'email')) {
                    $table->string('email');
                }
                if (!Schema::hasColumn('branches', 'address')) {
                    $table->text('address')->nullable();
                }
                if (!Schema::hasColumn('branches', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('branches', 'state')) {
                    $table->string('state')->nullable();
                }
                if (!Schema::hasColumn('branches', 'country')) {
                    $table->string('country')->nullable();
                }
                if (!Schema::hasColumn('branches', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('branches', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: brands
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('brands', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('brands', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('brands', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('brands', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('brands', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('brands', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id', 200)->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('categories', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('categories', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('categories', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('categories', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: chat_history
        if (!Schema::hasTable('chat_history')) {
            Schema::create('chat_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('session_id');
                $table->integer('page_id')->nullable();
                $table->text('user_question');
                $table->text('bot_response');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('chat_history', function (Blueprint $table) {
                if (!Schema::hasColumn('chat_history', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('chat_history', 'session_id')) {
                    $table->string('session_id');
                }
                if (!Schema::hasColumn('chat_history', 'page_id')) {
                    $table->integer('page_id')->nullable();
                }
                if (!Schema::hasColumn('chat_history', 'user_question')) {
                    $table->text('user_question');
                }
                if (!Schema::hasColumn('chat_history', 'bot_response')) {
                    $table->text('bot_response');
                }
                if (!Schema::hasColumn('chat_history', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('chat_history', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: chatbot_messages
        if (!Schema::hasTable('chatbot_messages')) {
            Schema::create('chatbot_messages', function (Blueprint $table) {
                $table->id();
                $table->text('user_message');
                $table->longText('bot_response');
                $table->string('session_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('chatbot_messages', function (Blueprint $table) {
                if (!Schema::hasColumn('chatbot_messages', 'user_message')) {
                    $table->text('user_message');
                }
                if (!Schema::hasColumn('chatbot_messages', 'bot_response')) {
                    $table->longText('bot_response');
                }
                if (!Schema::hasColumn('chatbot_messages', 'session_id')) {
                    $table->string('session_id')->nullable();
                }
                if (!Schema::hasColumn('chatbot_messages', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('chatbot_messages', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: code_master
        if (!Schema::hasTable('code_master')) {
            Schema::create('code_master', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('code_master', function (Blueprint $table) {
                if (!Schema::hasColumn('code_master', 'code')) {
                    $table->string('code')->nullable();
                }
                if (!Schema::hasColumn('code_master', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('code_master', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('code_master', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: daily_data
        if (!Schema::hasTable('daily_data')) {
            Schema::create('daily_data', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('treatment_id');
                $table->text('comment')->nullable();
                $table->date('date');
                $table->decimal('remain_amount', 10, 2);
                $table->unsignedBigInteger('collect_by_id');
                $table->text('status')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('daily_data', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_data', 'branch_id')) {
                    $table->string('branch_id');
                }
                if (!Schema::hasColumn('daily_data', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('daily_data', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('daily_data', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id');
                }
                if (!Schema::hasColumn('daily_data', 'comment')) {
                    $table->text('comment')->nullable();
                }
                if (!Schema::hasColumn('daily_data', 'date')) {
                    $table->date('date');
                }
                if (!Schema::hasColumn('daily_data', 'remain_amount')) {
                    $table->decimal('remain_amount', 10, 2);
                }
                if (!Schema::hasColumn('daily_data', 'collect_by_id')) {
                    $table->unsignedBigInteger('collect_by_id');
                }
                if (!Schema::hasColumn('daily_data', 'status')) {
                    $table->text('status')->nullable();
                }
                if (!Schema::hasColumn('daily_data', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('daily_data', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: default_email_template
        if (!Schema::hasTable('default_email_template')) {
            Schema::create('default_email_template', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->longText('content')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('default_email_template', function (Blueprint $table) {
                if (!Schema::hasColumn('default_email_template', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('default_email_template', 'content')) {
                    $table->longText('content')->nullable();
                }
                if (!Schema::hasColumn('default_email_template', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('default_email_template', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: diagnosis
        if (!Schema::hasTable('diagnosis')) {
            Schema::create('diagnosis', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('diagnosis', function (Blueprint $table) {
                if (!Schema::hasColumn('diagnosis', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('diagnosis', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('diagnosis', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('diagnosis', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('diagnosis', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: diet_charts
        if (!Schema::hasTable('diet_charts')) {
            Schema::create('diet_charts', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('name');
                $table->longText('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('image')->nullable();
                $table->longText('time')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('diet_charts', function (Blueprint $table) {
                if (!Schema::hasColumn('diet_charts', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('diet_charts', 'title')) {
                    $table->longText('title')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'image')) {
                    $table->longText('image')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'time')) {
                    $table->longText('time')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('diet_charts', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: email_template
        if (!Schema::hasTable('email_template')) {
            Schema::create('email_template', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('template_id');
                $table->string('name');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->text('img1')->nullable();
                $table->text('img2')->nullable();
                $table->text('img3')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('email_template', function (Blueprint $table) {
                if (!Schema::hasColumn('email_template', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('email_template', 'template_id')) {
                    $table->unsignedBigInteger('template_id');
                }
                if (!Schema::hasColumn('email_template', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('email_template', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('email_template', 'img1')) {
                    $table->text('img1')->nullable();
                }
                if (!Schema::hasColumn('email_template', 'img2')) {
                    $table->text('img2')->nullable();
                }
                if (!Schema::hasColumn('email_template', 'img3')) {
                    $table->text('img3')->nullable();
                }
                if (!Schema::hasColumn('email_template', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('email_template', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: email_templates
        if (!Schema::hasTable('email_templates')) {
            Schema::create('email_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->text('content');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('email_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('email_templates', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('email_templates', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('email_templates', 'content')) {
                    $table->text('content');
                }
                if (!Schema::hasColumn('email_templates', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('email_templates', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('email_templates', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: expenses
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->dateTime('date_time')->nullable();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->string('service')->nullable();
                $table->text('comment')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'date_time')) {
                    $table->dateTime('date_time')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'amount')) {
                    $table->decimal('amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('expenses', 'service')) {
                    $table->string('service')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'comment')) {
                    $table->text('comment')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: followups
        if (!Schema::hasTable('followups')) {
            Schema::create('followups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id');
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('treatment_id')->nullable();
                $table->unsignedBigInteger('doctor_id');
                $table->date('date');
                $table->text('followup_update')->nullable();
                $table->enum('followup_type', ['regular followup', 'appointment related', 'medicine related', 'discharge related', 'report related', 'other'])->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('followups', function (Blueprint $table) {
                if (!Schema::hasColumn('followups', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('followups', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id');
                }
                if (!Schema::hasColumn('followups', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('followups', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id')->nullable();
                }
                if (!Schema::hasColumn('followups', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('followups', 'date')) {
                    $table->date('date');
                }
                if (!Schema::hasColumn('followups', 'followup_update')) {
                    $table->text('followup_update')->nullable();
                }
                if (!Schema::hasColumn('followups', 'followup_type')) {
                    $table->enum('followup_type', ['regular followup', 'appointment related', 'medicine related', 'discharge related', 'report related', 'other'])->nullable();
                }
                if (!Schema::hasColumn('followups', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('followups', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: home_advice
        if (!Schema::hasTable('home_advice')) {
            Schema::create('home_advice', function (Blueprint $table) {
                $table->id();
                $table->string('template_name')->nullable();
                $table->longText('title')->nullable();
                $table->longText('description')->nullable();
                $table->longText('image')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('home_advice', function (Blueprint $table) {
                if (!Schema::hasColumn('home_advice', 'template_name')) {
                    $table->string('template_name')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'title')) {
                    $table->longText('title')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'image')) {
                    $table->longText('image')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('home_advice', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: inventory
        if (!Schema::hasTable('inventory')) {
            Schema::create('inventory', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('item_name');
                $table->integer('quantity');
                $table->unsignedBigInteger('supplier_id');
                $table->date('purchase_date');
                $table->enum('status', ['active', 'inactive', 'expired']);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('inventory', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('inventory', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('inventory', 'item_name')) {
                    $table->string('item_name');
                }
                if (!Schema::hasColumn('inventory', 'quantity')) {
                    $table->integer('quantity');
                }
                if (!Schema::hasColumn('inventory', 'supplier_id')) {
                    $table->unsignedBigInteger('supplier_id');
                }
                if (!Schema::hasColumn('inventory', 'purchase_date')) {
                    $table->date('purchase_date');
                }
                if (!Schema::hasColumn('inventory', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'expired']);
                }
                if (!Schema::hasColumn('inventory', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('inventory', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: invoices
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id', 200)->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->enum('type', ['appointment', 'service', 'treatment', 'medicine', 'followup'])->nullable();
                $table->unsignedBigInteger('type_id')->nullable();
                $table->date('date')->nullable();
                $table->text('instruction')->nullable();
                $table->longText('types_details')->nullable();
                $table->bigInteger('grand_total')->nullable();
                $table->string('payment_type')->nullable();
                $table->string('payment_status')->nullable();
                $table->boolean('gst_enabled')->default(1);
                $table->bigInteger('subtotal')->nullable();
                $table->bigInteger('tax')->nullable();
                $table->longText('discount_type')->nullable();
                $table->bigInteger('discount')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('invoices', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'type')) {
                    $table->enum('type', ['appointment', 'service', 'treatment', 'medicine', 'followup'])->nullable();
                }
                if (!Schema::hasColumn('invoices', 'type_id')) {
                    $table->unsignedBigInteger('type_id')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'date')) {
                    $table->date('date')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'instruction')) {
                    $table->text('instruction')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'types_details')) {
                    $table->longText('types_details')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'grand_total')) {
                    $table->bigInteger('grand_total')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'payment_type')) {
                    $table->string('payment_type')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'payment_status')) {
                    $table->string('payment_status')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'gst_enabled')) {
                    $table->boolean('gst_enabled')->default(1);
                }
                if (!Schema::hasColumn('invoices', 'subtotal')) {
                    $table->bigInteger('subtotal')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'tax')) {
                    $table->bigInteger('tax')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'discount_type')) {
                    $table->longText('discount_type')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'discount')) {
                    $table->bigInteger('discount')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: ipd_admissions
        if (!Schema::hasTable('ipd_admissions')) {
            Schema::create('ipd_admissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->string('branch_id', 200)->nullable();
                $table->unsignedBigInteger('doctor_id');
                $table->unsignedBigInteger('treatment_id');
                $table->date('admission_date');
                $table->date('discharge_date')->nullable();
                $table->string('room_number');
                $table->string('bed_number');
                $table->text('reason_for_admission')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('ipd_admissions', function (Blueprint $table) {
                if (!Schema::hasColumn('ipd_admissions', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('ipd_admissions', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('ipd_admissions', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('ipd_admissions', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id');
                }
                if (!Schema::hasColumn('ipd_admissions', 'admission_date')) {
                    $table->date('admission_date');
                }
                if (!Schema::hasColumn('ipd_admissions', 'discharge_date')) {
                    $table->date('discharge_date')->nullable();
                }
                if (!Schema::hasColumn('ipd_admissions', 'room_number')) {
                    $table->string('room_number');
                }
                if (!Schema::hasColumn('ipd_admissions', 'bed_number')) {
                    $table->string('bed_number');
                }
                if (!Schema::hasColumn('ipd_admissions', 'reason_for_admission')) {
                    $table->text('reason_for_admission')->nullable();
                }
                if (!Schema::hasColumn('ipd_admissions', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('ipd_admissions', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: jobs
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->id();
                $table->string('queue');
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
                $table->index(['queue'], 'jobs_queue_index');
            });
        } else {
            Schema::table('jobs', function (Blueprint $table) {
                if (!Schema::hasColumn('jobs', 'queue')) {
                    $table->string('queue');
                }
                if (!Schema::hasColumn('jobs', 'payload')) {
                    $table->longText('payload');
                }
                if (!Schema::hasColumn('jobs', 'attempts')) {
                    $table->unsignedTinyInteger('attempts');
                }
                if (!Schema::hasColumn('jobs', 'reserved_at')) {
                    $table->unsignedInteger('reserved_at')->nullable();
                }
                if (!Schema::hasColumn('jobs', 'available_at')) {
                    $table->unsignedInteger('available_at');
                }
                if (!Schema::hasColumn('jobs', 'created_at')) {
                    $table->unsignedInteger('created_at');
                }
            });
        }

        // Table: lab_tests
        if (!Schema::hasTable('lab_tests')) {
            Schema::create('lab_tests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->text('test_description')->nullable();
                $table->date('test_date')->nullable();
                $table->text('result')->nullable();
                $table->string('status')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('lab_tests', function (Blueprint $table) {
                if (!Schema::hasColumn('lab_tests', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'test_description')) {
                    $table->text('test_description')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'test_date')) {
                    $table->date('test_date')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'result')) {
                    $table->text('result')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'status')) {
                    $table->string('status')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('lab_tests', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: machines
        if (!Schema::hasTable('machines')) {
            Schema::create('machines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('name')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('machines', function (Blueprint $table) {
                if (!Schema::hasColumn('machines', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('machines', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('machines', 'price')) {
                    $table->decimal('price', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('machines', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('machines', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('machines', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: medical_reports
        if (!Schema::hasTable('medical_reports')) {
            Schema::create('medical_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->text('description')->nullable();
                $table->date('date')->nullable();
                $table->string('report_type', 200)->nullable();
                $table->string('file_path');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('medical_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('medical_reports', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('medical_reports', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('medical_reports', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'date')) {
                    $table->date('date')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'report_type')) {
                    $table->string('report_type', 200)->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'file_path')) {
                    $table->string('file_path');
                }
                if (!Schema::hasColumn('medical_reports', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('medical_reports', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: medicine_units
        if (!Schema::hasTable('medicine_units')) {
            Schema::create('medicine_units', function (Blueprint $table) {
                $table->id();
                $table->string('unit');
                $table->unsignedBigInteger('branch_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('medicine_units', function (Blueprint $table) {
                if (!Schema::hasColumn('medicine_units', 'unit')) {
                    $table->string('unit');
                }
                if (!Schema::hasColumn('medicine_units', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id');
                }
                if (!Schema::hasColumn('medicine_units', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('medicine_units', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: medicines
        if (!Schema::hasTable('medicines')) {
            Schema::create('medicines', function (Blueprint $table) {
                $table->id();
                $table->string('service_type')->default('Medicine');
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('unit');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('availablility')->default('in_stock');
                $table->boolean('is_featured')->default(0);
                $table->integer('quantity')->default(0);
                $table->integer('low_stock_qty')->nullable();
                $table->string('medicine_unit');
                $table->unsignedBigInteger('stock_unit_id')->nullable();
                $table->string('batch_no');
                $table->string('barcode')->nullable();
                $table->string('pack')->nullable();
                $table->string('hsn_code')->nullable();
                $table->string('gst_option');
                $table->text('product_gst')->nullable();
                $table->decimal('mrp', 12, 2)->nullable();
                $table->decimal('purchase_price', 12, 2)->nullable();
                $table->decimal('stock_value', 12, 2)->nullable();
                $table->date('manufacture_date');
                $table->date('expiry_date');
                $table->text('image')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('medicines', function (Blueprint $table) {
                if (!Schema::hasColumn('medicines', 'service_type')) {
                    $table->string('service_type')->default('Medicine');
                }
                if (!Schema::hasColumn('medicines', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('medicines', 'category_id')) {
                    $table->unsignedBigInteger('category_id');
                }
                if (!Schema::hasColumn('medicines', 'brand_id')) {
                    $table->unsignedBigInteger('brand_id')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('medicines', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'unit')) {
                    $table->string('unit');
                }
                if (!Schema::hasColumn('medicines', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('medicines', 'availablility')) {
                    $table->string('availablility')->default('in_stock');
                }
                if (!Schema::hasColumn('medicines', 'is_featured')) {
                    $table->boolean('is_featured')->default(0);
                }
                if (!Schema::hasColumn('medicines', 'quantity')) {
                    $table->integer('quantity')->default(0);
                }
                if (!Schema::hasColumn('medicines', 'low_stock_qty')) {
                    $table->integer('low_stock_qty')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'medicine_unit')) {
                    $table->string('medicine_unit');
                }
                if (!Schema::hasColumn('medicines', 'stock_unit_id')) {
                    $table->unsignedBigInteger('stock_unit_id')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'batch_no')) {
                    $table->string('batch_no');
                }
                if (!Schema::hasColumn('medicines', 'barcode')) {
                    $table->string('barcode')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'pack')) {
                    $table->string('pack')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'hsn_code')) {
                    $table->string('hsn_code')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'gst_option')) {
                    $table->string('gst_option');
                }
                if (!Schema::hasColumn('medicines', 'product_gst')) {
                    $table->text('product_gst')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'mrp')) {
                    $table->decimal('mrp', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('medicines', 'purchase_price')) {
                    $table->decimal('purchase_price', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('medicines', 'stock_value')) {
                    $table->decimal('stock_value', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('medicines', 'manufacture_date')) {
                    $table->date('manufacture_date');
                }
                if (!Schema::hasColumn('medicines', 'expiry_date')) {
                    $table->date('expiry_date');
                }
                if (!Schema::hasColumn('medicines', 'image')) {
                    $table->text('image')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('medicines', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: modules
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('modules', function (Blueprint $table) {
                if (!Schema::hasColumn('modules', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('modules', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('modules', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->unsignedBigInteger('receiver_id')->nullable();
                $table->longText('info');
                $table->boolean('is_read')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['sender_id'], 'notifications_sender_id_foreign');
                $table->index(['receiver_id'], 'notifications_receiver_id_foreign');
            });
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('notifications', 'sender_id')) {
                    $table->unsignedBigInteger('sender_id')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'receiver_id')) {
                    $table->unsignedBigInteger('receiver_id')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'info')) {
                    $table->longText('info');
                }
                if (!Schema::hasColumn('notifications', 'is_read')) {
                    $table->boolean('is_read')->default(0);
                }
                if (!Schema::hasColumn('notifications', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: opd_visits
        if (!Schema::hasTable('opd_visits')) {
            Schema::create('opd_visits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->string('branch_id', 200)->nullable();
                $table->unsignedBigInteger('doctor_id');
                $table->date('visit_date');
                $table->text('chief_complaint')->nullable();
                $table->text('diagnosis')->nullable();
                $table->longText('prescription')->nullable();
                $table->decimal('consultation_fees', 8, 2)->default(0.00);
                $table->enum('status', ['active', 'completed'])->default('active');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('opd_visits', function (Blueprint $table) {
                if (!Schema::hasColumn('opd_visits', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('opd_visits', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('opd_visits', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('opd_visits', 'visit_date')) {
                    $table->date('visit_date');
                }
                if (!Schema::hasColumn('opd_visits', 'chief_complaint')) {
                    $table->text('chief_complaint')->nullable();
                }
                if (!Schema::hasColumn('opd_visits', 'diagnosis')) {
                    $table->text('diagnosis')->nullable();
                }
                if (!Schema::hasColumn('opd_visits', 'prescription')) {
                    $table->longText('prescription')->nullable();
                }
                if (!Schema::hasColumn('opd_visits', 'consultation_fees')) {
                    $table->decimal('consultation_fees', 8, 2)->default(0.00);
                }
                if (!Schema::hasColumn('opd_visits', 'status')) {
                    $table->enum('status', ['active', 'completed'])->default('active');
                }
                if (!Schema::hasColumn('opd_visits', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('opd_visits', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: order_items
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('custom_product_name')->nullable();
                $table->boolean('is_custom_product')->default(0);
                $table->string('batch_number')->nullable();
                $table->date('expiry_date')->nullable();
                $table->longText('product_gst_details')->nullable();
                $table->decimal('product_gst_total', 10, 2)->nullable()->default(0.00);
                $table->decimal('product_discount', 10, 2)->nullable()->default(0.00);
                $table->decimal('product_discount_price', 10, 2)->nullable()->default(0.00);
                $table->longText('patient_ids')->nullable();
                $table->integer('scheme_free_qty')->nullable()->default(0);
                $table->decimal('scheme_rate', 10, 2)->nullable()->default(0.00);
                $table->integer('free_qty')->nullable()->default(0);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->decimal('price', 12, 2)->default(0.00);
                $table->decimal('mrp', 12, 2)->nullable()->default(0.00);
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('sale_type')->default('local');
                $table->boolean('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['order_id'], 'order_items_order_id_foreign');
            });
        } else {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'order_id')) {
                    $table->unsignedBigInteger('order_id');
                }
                if (!Schema::hasColumn('order_items', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'custom_product_name')) {
                    $table->string('custom_product_name')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'is_custom_product')) {
                    $table->boolean('is_custom_product')->default(0);
                }
                if (!Schema::hasColumn('order_items', 'batch_number')) {
                    $table->string('batch_number')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'expiry_date')) {
                    $table->date('expiry_date')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'product_gst_details')) {
                    $table->longText('product_gst_details')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'product_gst_total')) {
                    $table->decimal('product_gst_total', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'product_discount')) {
                    $table->decimal('product_discount', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'product_discount_price')) {
                    $table->decimal('product_discount_price', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'patient_ids')) {
                    $table->longText('patient_ids')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'scheme_free_qty')) {
                    $table->integer('scheme_free_qty')->nullable()->default(0);
                }
                if (!Schema::hasColumn('order_items', 'scheme_rate')) {
                    $table->decimal('scheme_rate', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'free_qty')) {
                    $table->integer('free_qty')->nullable()->default(0);
                }
                if (!Schema::hasColumn('order_items', 'category_id')) {
                    $table->unsignedBigInteger('category_id')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'quantity')) {
                    $table->integer('quantity')->default(1);
                }
                if (!Schema::hasColumn('order_items', 'price')) {
                    $table->decimal('price', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'mrp')) {
                    $table->decimal('mrp', 12, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('order_items', 'branch_id')) {
                    $table->bigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'sale_type')) {
                    $table->string('sale_type')->default('local');
                }
                if (!Schema::hasColumn('order_items', 'isDeleted')) {
                    $table->boolean('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('order_items', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('order_items', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: order_patient
        if (!Schema::hasTable('order_patient')) {
            Schema::create('order_patient', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('patient_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['order_id', 'patient_id'], 'order_patient_order_id_patient_id_unique');
                $table->index(['patient_id'], 'order_patient_patient_id_foreign');
            });
        } else {
            Schema::table('order_patient', function (Blueprint $table) {
                if (!Schema::hasColumn('order_patient', 'order_id')) {
                    $table->unsignedBigInteger('order_id');
                }
                if (!Schema::hasColumn('order_patient', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('order_patient', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('order_patient', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->date('order_date')->nullable();
                $table->string('order_type')->nullable();
                $table->integer('is_custom')->default(0);
                $table->decimal('discount', 10, 2)->nullable()->default(0.00);
                $table->decimal('discount_percent', 10, 2)->nullable();
                $table->decimal('discount_amount', 10, 2)->nullable();
                $table->decimal('round_off', 8, 2)->nullable()->default(0.00);
                $table->string('shipping')->nullable();
                $table->string('misc_charges')->nullable();
                $table->text('tax_id')->nullable();
                $table->string('gst_option')->default('without_gst');
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('remaining_amount', 12, 2)->default(0.00);
                $table->string('payment_status')->default('pending');
                $table->string('payment_method')->default('cash');
                $table->string('order_invoice')->nullable();
                $table->string('hospital_status')->nullable();
                $table->string('admin_status')->nullable();
                $table->tinyInteger('admin_approve')->default(0);
                $table->string('delivered_status')->default('delivered');
                $table->text('remarks')->nullable();
                $table->tinyInteger('is_emergency')->default(0);
                $table->tinyInteger('online_portal_sales')->default(0);
                $table->tinyInteger('export_sales')->default(0);
                $table->text('rate_description')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['order_number'], 'orders_order_number_unique');
            });
        } else {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'order_number')) {
                    $table->string('order_number');
                }
                if (!Schema::hasColumn('orders', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'company_id')) {
                    $table->unsignedBigInteger('company_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'order_date')) {
                    $table->date('order_date')->nullable();
                }
                if (!Schema::hasColumn('orders', 'order_type')) {
                    $table->string('order_type')->nullable();
                }
                if (!Schema::hasColumn('orders', 'is_custom')) {
                    $table->integer('is_custom')->default(0);
                }
                if (!Schema::hasColumn('orders', 'discount')) {
                    $table->decimal('discount', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('orders', 'discount_percent')) {
                    $table->decimal('discount_percent', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('orders', 'round_off')) {
                    $table->decimal('round_off', 8, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('orders', 'shipping')) {
                    $table->string('shipping')->nullable();
                }
                if (!Schema::hasColumn('orders', 'misc_charges')) {
                    $table->string('misc_charges')->nullable();
                }
                if (!Schema::hasColumn('orders', 'tax_id')) {
                    $table->text('tax_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'gst_option')) {
                    $table->string('gst_option')->default('without_gst');
                }
                if (!Schema::hasColumn('orders', 'branch_id')) {
                    $table->bigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('orders', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('orders', 'remaining_amount')) {
                    $table->decimal('remaining_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('orders', 'payment_status')) {
                    $table->string('payment_status')->default('pending');
                }
                if (!Schema::hasColumn('orders', 'payment_method')) {
                    $table->string('payment_method')->default('cash');
                }
                if (!Schema::hasColumn('orders', 'order_invoice')) {
                    $table->string('order_invoice')->nullable();
                }
                if (!Schema::hasColumn('orders', 'hospital_status')) {
                    $table->string('hospital_status')->nullable();
                }
                if (!Schema::hasColumn('orders', 'admin_status')) {
                    $table->string('admin_status')->nullable();
                }
                if (!Schema::hasColumn('orders', 'admin_approve')) {
                    $table->tinyInteger('admin_approve')->default(0);
                }
                if (!Schema::hasColumn('orders', 'delivered_status')) {
                    $table->string('delivered_status')->default('delivered');
                }
                if (!Schema::hasColumn('orders', 'remarks')) {
                    $table->text('remarks')->nullable();
                }
                if (!Schema::hasColumn('orders', 'is_emergency')) {
                    $table->tinyInteger('is_emergency')->default(0);
                }
                if (!Schema::hasColumn('orders', 'online_portal_sales')) {
                    $table->tinyInteger('online_portal_sales')->default(0);
                }
                if (!Schema::hasColumn('orders', 'export_sales')) {
                    $table->tinyInteger('export_sales')->default(0);
                }
                if (!Schema::hasColumn('orders', 'rate_description')) {
                    $table->text('rate_description')->nullable();
                }
                if (!Schema::hasColumn('orders', 'isDeleted')) {
                    $table->boolean('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('orders', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('orders', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: ot_procedures
        if (!Schema::hasTable('ot_procedures')) {
            Schema::create('ot_procedures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id');
                $table->string('branch_id', 200)->nullable();
                $table->unsignedBigInteger('doctor_id');
                $table->string('procedure_name');
                $table->date('procedure_date');
                $table->text('operation_notes')->nullable();
                $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
                $table->date('updated_at')->nullable();
                $table->date('created_at')->nullable();
            });
        } else {
            Schema::table('ot_procedures', function (Blueprint $table) {
                if (!Schema::hasColumn('ot_procedures', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('ot_procedures', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('ot_procedures', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('ot_procedures', 'procedure_name')) {
                    $table->string('procedure_name');
                }
                if (!Schema::hasColumn('ot_procedures', 'procedure_date')) {
                    $table->date('procedure_date');
                }
                if (!Schema::hasColumn('ot_procedures', 'operation_notes')) {
                    $table->text('operation_notes')->nullable();
                }
                if (!Schema::hasColumn('ot_procedures', 'status')) {
                    $table->enum('status', ['scheduled', 'completed'])->default('scheduled');
                }
                if (!Schema::hasColumn('ot_procedures', 'updated_at')) {
                    $table->date('updated_at')->nullable();
                }
                if (!Schema::hasColumn('ot_procedures', 'created_at')) {
                    $table->date('created_at')->nullable();
                }
            });
        }

        // Table: pathology_reports
        if (!Schema::hasTable('pathology_reports')) {
            Schema::create('pathology_reports', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('test_id');
                $table->date('sample_collected_date');
                $table->date('report_date');
                $table->text('result');
                $table->string('report_file');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('pathology_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('pathology_reports', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('pathology_reports', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('pathology_reports', 'test_id')) {
                    $table->unsignedBigInteger('test_id');
                }
                if (!Schema::hasColumn('pathology_reports', 'sample_collected_date')) {
                    $table->date('sample_collected_date');
                }
                if (!Schema::hasColumn('pathology_reports', 'report_date')) {
                    $table->date('report_date');
                }
                if (!Schema::hasColumn('pathology_reports', 'result')) {
                    $table->text('result');
                }
                if (!Schema::hasColumn('pathology_reports', 'report_file')) {
                    $table->string('report_file');
                }
                if (!Schema::hasColumn('pathology_reports', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('pathology_reports', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: pathology_tests
        if (!Schema::hasTable('pathology_tests')) {
            Schema::create('pathology_tests', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('test_name');
                $table->string('test_code');
                $table->string('sample_type');
                $table->string('normal_range');
                $table->decimal('cost', 8, 2);
                $table->string('gst_option');
                $table->text('product_gst')->nullable();
                $table->string('report_format')->default('PDF');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['test_code'], 'pathology_tests_test_code_unique');
            });
        } else {
            Schema::table('pathology_tests', function (Blueprint $table) {
                if (!Schema::hasColumn('pathology_tests', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('pathology_tests', 'test_name')) {
                    $table->string('test_name');
                }
                if (!Schema::hasColumn('pathology_tests', 'test_code')) {
                    $table->string('test_code');
                }
                if (!Schema::hasColumn('pathology_tests', 'sample_type')) {
                    $table->string('sample_type');
                }
                if (!Schema::hasColumn('pathology_tests', 'normal_range')) {
                    $table->string('normal_range');
                }
                if (!Schema::hasColumn('pathology_tests', 'cost')) {
                    $table->decimal('cost', 8, 2);
                }
                if (!Schema::hasColumn('pathology_tests', 'gst_option')) {
                    $table->string('gst_option');
                }
                if (!Schema::hasColumn('pathology_tests', 'product_gst')) {
                    $table->text('product_gst')->nullable();
                }
                if (!Schema::hasColumn('pathology_tests', 'report_format')) {
                    $table->string('report_format')->default('PDF');
                }
                if (!Schema::hasColumn('pathology_tests', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('pathology_tests', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_assign_assessment
        if (!Schema::hasTable('patient_assign_assessment')) {
            Schema::create('patient_assign_assessment', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->longText('template_id')->nullable();
                $table->longText('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_assign_assessment', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_assign_assessment', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_assessment', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_assessment', 'template_id')) {
                    $table->longText('template_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_assessment', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_assessment', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_assessment', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_assign_dietchart
        if (!Schema::hasTable('patient_assign_dietchart')) {
            Schema::create('patient_assign_dietchart', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id');
                $table->longText('diet_template_id')->nullable();
                $table->longText('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_assign_dietchart', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_assign_dietchart', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_dietchart', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('patient_assign_dietchart', 'diet_template_id')) {
                    $table->longText('diet_template_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_dietchart', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_dietchart', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_dietchart', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_assign_homeadvice
        if (!Schema::hasTable('patient_assign_homeadvice')) {
            Schema::create('patient_assign_homeadvice', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->longText('template_id')->nullable();
                $table->longText('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_assign_homeadvice', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_assign_homeadvice', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_homeadvice', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_homeadvice', 'template_id')) {
                    $table->longText('template_id')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_homeadvice', 'description')) {
                    $table->longText('description')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_homeadvice', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_assign_homeadvice', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_diagnosis
        if (!Schema::hasTable('patient_diagnosis')) {
            Schema::create('patient_diagnosis', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->unsignedBigInteger('branch_id');
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_diagnosis', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_diagnosis', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('patient_diagnosis', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id');
                }
                if (!Schema::hasColumn('patient_diagnosis', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('patient_diagnosis', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_diagnosis', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_discharge_dets
        if (!Schema::hasTable('patient_discharge_dets')) {
            Schema::create('patient_discharge_dets', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id', 200)->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('treatment_id')->nullable();
                $table->string('ipd_id', 200)->nullable();
                $table->string('gst_option', 200)->nullable();
                $table->string('product_gst', 200)->nullable();
                $table->string('room_number')->nullable();
                $table->string('bed_number')->nullable();
                $table->date('admit_date')->nullable();
                $table->date('discharge_date');
                $table->decimal('total_bill', 10, 2)->default(0.00);
                $table->decimal('amount_paid', 10, 2)->default(0.00);
                $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
                $table->text('discharge_note')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_discharge_dets', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_discharge_dets', 'branch_id')) {
                    $table->string('branch_id', 200)->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'ipd_id')) {
                    $table->string('ipd_id', 200)->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'gst_option')) {
                    $table->string('gst_option', 200)->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'product_gst')) {
                    $table->string('product_gst', 200)->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'room_number')) {
                    $table->string('room_number')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'bed_number')) {
                    $table->string('bed_number')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'admit_date')) {
                    $table->date('admit_date')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'discharge_date')) {
                    $table->date('discharge_date');
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'total_bill')) {
                    $table->decimal('total_bill', 10, 2)->default(0.00);
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'amount_paid')) {
                    $table->decimal('amount_paid', 10, 2)->default(0.00);
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'discharge_note')) {
                    $table->text('discharge_note')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_discharge_dets', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patient_medicine
        if (!Schema::hasTable('patient_medicine')) {
            Schema::create('patient_medicine', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->longText('medicine_id')->nullable();
                $table->unsignedBigInteger('treatment_id')->nullable();
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->text('code')->nullable();
                $table->text('value')->nullable();
                $table->text('value_type')->nullable();
                $table->text('note')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('patient_medicine', function (Blueprint $table) {
                if (!Schema::hasColumn('patient_medicine', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'medicine_id')) {
                    $table->longText('medicine_id')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'appointment_id')) {
                    $table->unsignedBigInteger('appointment_id')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'code')) {
                    $table->text('code')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'value')) {
                    $table->text('value')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'value_type')) {
                    $table->text('value_type')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'note')) {
                    $table->text('note')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patient_medicine', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: patients
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->text('patient_unique_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('login_patient_id')->nullable();
                $table->string('treatment_id', 200)->nullable();
                $table->string('fullname')->nullable();
                $table->text('email')->nullable();
                $table->string('password')->nullable();
                $table->string('phone')->nullable();
                $table->string('bp', 50)->nullable();
                $table->string('temperature', 50)->nullable();
                $table->string('pulse', 50)->nullable();
                $table->text('patient_history')->nullable();
                $table->text('patient_complaint')->nullable();
                $table->text('advice_for_lab_tests')->nullable();
                $table->string('address')->nullable();
                $table->string('age')->nullable();
                $table->date('birthdate')->nullable();
                $table->string('gender')->nullable();
                $table->enum('patient_type', ['all', 'cosmetic', 'opd', 'ipd', 'home'])->nullable();
                $table->string('referral_source')->nullable();
                $table->text('source_details')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->string('diagnosis_id')->nullable();
                $table->text('last_appointment')->nullable();
                $table->text('profile')->nullable();
                $table->string('note_type')->nullable();
                $table->string('note')->nullable();
                $table->string('note_interval', 500)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->string('symptoms', 1000)->nullable();
                $table->string('improvement_history', 500)->nullable();
                $table->string('symptom_images', 500)->nullable();
                $table->text('symptom_remarks')->nullable();
                $table->string('weight')->nullable();
                $table->string('height')->nullable();
            });
        } else {
            Schema::table('patients', function (Blueprint $table) {
                if (!Schema::hasColumn('patients', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('patients', 'patient_unique_id')) {
                    $table->text('patient_unique_id')->nullable();
                }
                if (!Schema::hasColumn('patients', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('patients', 'login_patient_id')) {
                    $table->unsignedBigInteger('login_patient_id')->nullable();
                }
                if (!Schema::hasColumn('patients', 'treatment_id')) {
                    $table->string('treatment_id', 200)->nullable();
                }
                if (!Schema::hasColumn('patients', 'fullname')) {
                    $table->string('fullname')->nullable();
                }
                if (!Schema::hasColumn('patients', 'email')) {
                    $table->text('email')->nullable();
                }
                if (!Schema::hasColumn('patients', 'password')) {
                    $table->string('password')->nullable();
                }
                if (!Schema::hasColumn('patients', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('patients', 'bp')) {
                    $table->string('bp', 50)->nullable();
                }
                if (!Schema::hasColumn('patients', 'temperature')) {
                    $table->string('temperature', 50)->nullable();
                }
                if (!Schema::hasColumn('patients', 'pulse')) {
                    $table->string('pulse', 50)->nullable();
                }
                if (!Schema::hasColumn('patients', 'patient_history')) {
                    $table->text('patient_history')->nullable();
                }
                if (!Schema::hasColumn('patients', 'patient_complaint')) {
                    $table->text('patient_complaint')->nullable();
                }
                if (!Schema::hasColumn('patients', 'advice_for_lab_tests')) {
                    $table->text('advice_for_lab_tests')->nullable();
                }
                if (!Schema::hasColumn('patients', 'address')) {
                    $table->string('address')->nullable();
                }
                if (!Schema::hasColumn('patients', 'age')) {
                    $table->string('age')->nullable();
                }
                if (!Schema::hasColumn('patients', 'birthdate')) {
                    $table->date('birthdate')->nullable();
                }
                if (!Schema::hasColumn('patients', 'gender')) {
                    $table->string('gender')->nullable();
                }
                if (!Schema::hasColumn('patients', 'patient_type')) {
                    $table->enum('patient_type', ['all', 'cosmetic', 'opd', 'ipd', 'home'])->nullable();
                }
                if (!Schema::hasColumn('patients', 'referral_source')) {
                    $table->string('referral_source')->nullable();
                }
                if (!Schema::hasColumn('patients', 'source_details')) {
                    $table->text('source_details')->nullable();
                }
                if (!Schema::hasColumn('patients', 'state')) {
                    $table->string('state')->nullable();
                }
                if (!Schema::hasColumn('patients', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('patients', 'diagnosis_id')) {
                    $table->string('diagnosis_id')->nullable();
                }
                if (!Schema::hasColumn('patients', 'last_appointment')) {
                    $table->text('last_appointment')->nullable();
                }
                if (!Schema::hasColumn('patients', 'profile')) {
                    $table->text('profile')->nullable();
                }
                if (!Schema::hasColumn('patients', 'note_type')) {
                    $table->string('note_type')->nullable();
                }
                if (!Schema::hasColumn('patients', 'note')) {
                    $table->string('note')->nullable();
                }
                if (!Schema::hasColumn('patients', 'note_interval')) {
                    $table->string('note_interval', 500)->nullable();
                }
                if (!Schema::hasColumn('patients', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('patients', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
                if (!Schema::hasColumn('patients', 'symptoms')) {
                    $table->string('symptoms', 1000)->nullable();
                }
                if (!Schema::hasColumn('patients', 'improvement_history')) {
                    $table->string('improvement_history', 500)->nullable();
                }
                if (!Schema::hasColumn('patients', 'symptom_images')) {
                    $table->string('symptom_images', 500)->nullable();
                }
                if (!Schema::hasColumn('patients', 'symptom_remarks')) {
                    $table->text('symptom_remarks')->nullable();
                }
                if (!Schema::hasColumn('patients', 'weight')) {
                    $table->string('weight')->nullable();
                }
                if (!Schema::hasColumn('patients', 'height')) {
                    $table->string('height')->nullable();
                }
            });
        }

        // Table: payment_history
        if (!Schema::hasTable('payment_history')) {
            Schema::create('payment_history', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('daily_id');
                $table->decimal('amount', 10, 2);
                $table->enum('payment_mode', ['cash', 'online', 'cash+online']);
                $table->enum('paid_type', ['fully', 'partial']);
                $table->integer('cash_amount')->nullable();
                $table->integer('online_amount')->nullable();
                $table->decimal('paid_amount', 10, 2);
                $table->decimal('remain_amount', 10, 2)->default(0.00);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('payment_history', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_history', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('payment_history', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('payment_history', 'daily_id')) {
                    $table->unsignedBigInteger('daily_id');
                }
                if (!Schema::hasColumn('payment_history', 'amount')) {
                    $table->decimal('amount', 10, 2);
                }
                if (!Schema::hasColumn('payment_history', 'payment_mode')) {
                    $table->enum('payment_mode', ['cash', 'online', 'cash+online']);
                }
                if (!Schema::hasColumn('payment_history', 'paid_type')) {
                    $table->enum('paid_type', ['fully', 'partial']);
                }
                if (!Schema::hasColumn('payment_history', 'cash_amount')) {
                    $table->integer('cash_amount')->nullable();
                }
                if (!Schema::hasColumn('payment_history', 'online_amount')) {
                    $table->integer('online_amount')->nullable();
                }
                if (!Schema::hasColumn('payment_history', 'paid_amount')) {
                    $table->decimal('paid_amount', 10, 2);
                }
                if (!Schema::hasColumn('payment_history', 'remain_amount')) {
                    $table->decimal('remain_amount', 10, 2)->default(0.00);
                }
                if (!Schema::hasColumn('payment_history', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('payment_history', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: payment_store
        if (!Schema::hasTable('payment_store')) {
            Schema::create('payment_store', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->decimal('payment_amount', 12, 2)->default(0.00);
                $table->date('payment_date');
                $table->string('payment_type')->default('order');
                $table->string('payment_method')->default('cash');
                $table->string('cash_amount')->default(0);
                $table->string('upi_amount')->default(0);
                $table->string('emi_month')->nullable();
                $table->string('remaining_amount')->default(0);
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('payment_store', function (Blueprint $table) {
                if (!Schema::hasColumn('payment_store', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'order_id')) {
                    $table->unsignedBigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'invoice_id')) {
                    $table->unsignedBigInteger('invoice_id')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'payment_amount')) {
                    $table->decimal('payment_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('payment_store', 'payment_date')) {
                    $table->date('payment_date');
                }
                if (!Schema::hasColumn('payment_store', 'payment_type')) {
                    $table->string('payment_type')->default('order');
                }
                if (!Schema::hasColumn('payment_store', 'payment_method')) {
                    $table->string('payment_method')->default('cash');
                }
                if (!Schema::hasColumn('payment_store', 'cash_amount')) {
                    $table->string('cash_amount')->default(0);
                }
                if (!Schema::hasColumn('payment_store', 'upi_amount')) {
                    $table->string('upi_amount')->default(0);
                }
                if (!Schema::hasColumn('payment_store', 'emi_month')) {
                    $table->string('emi_month')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'remaining_amount')) {
                    $table->string('remaining_amount')->default(0);
                }
                if (!Schema::hasColumn('payment_store', 'remarks')) {
                    $table->text('remarks')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'isDeleted')) {
                    $table->unsignedBigInteger('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('payment_store', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('payment_store', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: personal_access_tokens
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('tokenable_type');
                $table->unsignedBigInteger('tokenable_id');
                $table->string('name');
                $table->string('token', 64);
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['token'], 'personal_access_tokens_token_unique');
                $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
            });
        } else {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                if (!Schema::hasColumn('personal_access_tokens', 'tokenable_type')) {
                    $table->string('tokenable_type');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'tokenable_id')) {
                    $table->unsignedBigInteger('tokenable_id');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('personal_access_tokens', 'token')) {
                    $table->string('token', 64);
                }
                if (!Schema::hasColumn('personal_access_tokens', 'abilities')) {
                    $table->text('abilities')->nullable();
                }
                if (!Schema::hasColumn('personal_access_tokens', 'last_used_at')) {
                    $table->timestamp('last_used_at')->nullable();
                }
                if (!Schema::hasColumn('personal_access_tokens', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable();
                }
                if (!Schema::hasColumn('personal_access_tokens', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('personal_access_tokens', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: plans
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('price', 10, 2)->nullable();
                $table->string('duration')->default('month');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('subtitle')->nullable();
                $table->integer('user_limit')->nullable();
                $table->integer('branch_limit')->nullable();
                $table->integer('storage_limit')->nullable();
                $table->boolean('is_active')->default(1);
                $table->unsignedBigInteger('sub_branch_id')->nullable();
                $table->longText('features')->nullable();
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('plans', function (Blueprint $table) {
                if (!Schema::hasColumn('plans', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('plans', 'price')) {
                    $table->decimal('price', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('plans', 'duration')) {
                    $table->string('duration')->default('month');
                }
                if (!Schema::hasColumn('plans', 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn('plans', 'end_date')) {
                    $table->date('end_date')->nullable();
                }
                if (!Schema::hasColumn('plans', 'subtitle')) {
                    $table->string('subtitle')->nullable();
                }
                if (!Schema::hasColumn('plans', 'user_limit')) {
                    $table->integer('user_limit')->nullable();
                }
                if (!Schema::hasColumn('plans', 'branch_limit')) {
                    $table->integer('branch_limit')->nullable();
                }
                if (!Schema::hasColumn('plans', 'storage_limit')) {
                    $table->integer('storage_limit')->nullable();
                }
                if (!Schema::hasColumn('plans', 'is_active')) {
                    $table->boolean('is_active')->default(1);
                }
                if (!Schema::hasColumn('plans', 'sub_branch_id')) {
                    $table->unsignedBigInteger('sub_branch_id')->nullable();
                }
                if (!Schema::hasColumn('plans', 'features')) {
                    $table->longText('features')->nullable();
                }
                if (!Schema::hasColumn('plans', 'total_amount')) {
                    $table->decimal('total_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('plans', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('plans', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: prescriptions
        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('dosage')->nullable();
                $table->string('frequency')->nullable();
                $table->string('duration')->nullable();
                $table->text('instruction')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('prescriptions', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'medicine_id')) {
                    $table->unsignedBigInteger('medicine_id')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'dosage')) {
                    $table->string('dosage')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'frequency')) {
                    $table->string('frequency')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'duration')) {
                    $table->string('duration')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'instruction')) {
                    $table->text('instruction')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('prescriptions', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: product_barcodes
        if (!Schema::hasTable('product_barcodes')) {
            Schema::create('product_barcodes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('barcode');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('product_barcodes', function (Blueprint $table) {
                if (!Schema::hasColumn('product_barcodes', 'medicine_id')) {
                    $table->unsignedBigInteger('medicine_id')->nullable();
                }
                if (!Schema::hasColumn('product_barcodes', 'barcode')) {
                    $table->string('barcode');
                }
                if (!Schema::hasColumn('product_barcodes', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('product_barcodes', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: product_documentations
        if (!Schema::hasTable('product_documentations')) {
            Schema::create('product_documentations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('batch_number')->nullable();
                $table->date('mfg_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->longText('coa_pdfs')->nullable();
                $table->longText('extraction_metadata')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['product_id'], 'product_documentations_product_id_index');
                $table->index(['order_id'], 'product_documentations_order_id_index');
            });
        } else {
            Schema::table('product_documentations', function (Blueprint $table) {
                if (!Schema::hasColumn('product_documentations', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'order_id')) {
                    $table->unsignedBigInteger('order_id')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'batch_number')) {
                    $table->string('batch_number')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'mfg_date')) {
                    $table->date('mfg_date')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'expiry_date')) {
                    $table->date('expiry_date')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'coa_pdfs')) {
                    $table->longText('coa_pdfs')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'extraction_metadata')) {
                    $table->longText('extraction_metadata')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('product_documentations', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: project_types
        if (!Schema::hasTable('project_types')) {
            Schema::create('project_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code');
                $table->string('icon')->nullable();
                $table->boolean('status')->default(1);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['code'], 'project_types_code_unique');
            });
        } else {
            Schema::table('project_types', function (Blueprint $table) {
                if (!Schema::hasColumn('project_types', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('project_types', 'code')) {
                    $table->string('code');
                }
                if (!Schema::hasColumn('project_types', 'icon')) {
                    $table->string('icon')->nullable();
                }
                if (!Schema::hasColumn('project_types', 'status')) {
                    $table->boolean('status')->default(1);
                }
                if (!Schema::hasColumn('project_types', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('project_types', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: purchase_invoice
        if (!Schema::hasTable('purchase_invoice')) {
            Schema::create('purchase_invoice', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number');
                $table->unsignedBigInteger('vendor_id');
                $table->string('bill_no')->nullable();
                $table->longText('products')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0.00);
                $table->decimal('paid', 12, 2)->nullable()->default(0.00);
                $table->decimal('discount', 10, 2)->nullable()->default(0.00);
                $table->decimal('shipping', 10, 2)->nullable()->default(0.00);
                $table->text('taxes')->nullable();
                $table->decimal('grand_total', 12, 2)->default(0.00);
                $table->decimal('remaining_amount', 12, 2)->nullable()->default(0.00);
                $table->string('gst_option')->default('without_gst');
                $table->string('status')->default('completed');
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->integer('payment_due_days')->nullable();
                $table->date('due_date')->nullable();
                $table->date('payment_completed_date')->nullable();
                $table->string('interest_type')->nullable();
                $table->decimal('interest_amount', 10, 2)->nullable()->default(0.00);
                $table->decimal('final_amount_after_interest', 12, 2)->nullable();
                $table->longText('extraction_metadata')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['invoice_number'], 'purchase_invoice_invoice_number_unique');
            });
        } else {
            Schema::table('purchase_invoice', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_invoice', 'invoice_number')) {
                    $table->string('invoice_number');
                }
                if (!Schema::hasColumn('purchase_invoice', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id');
                }
                if (!Schema::hasColumn('purchase_invoice', 'bill_no')) {
                    $table->string('bill_no')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'products')) {
                    $table->longText('products')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'total_amount')) {
                    $table->decimal('total_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'paid')) {
                    $table->decimal('paid', 12, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'discount')) {
                    $table->decimal('discount', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'shipping')) {
                    $table->decimal('shipping', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'taxes')) {
                    $table->text('taxes')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'grand_total')) {
                    $table->decimal('grand_total', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'remaining_amount')) {
                    $table->decimal('remaining_amount', 12, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'gst_option')) {
                    $table->string('gst_option')->default('without_gst');
                }
                if (!Schema::hasColumn('purchase_invoice', 'status')) {
                    $table->string('status')->default('completed');
                }
                if (!Schema::hasColumn('purchase_invoice', 'branch_id')) {
                    $table->bigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'payment_due_days')) {
                    $table->integer('payment_due_days')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'due_date')) {
                    $table->date('due_date')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'payment_completed_date')) {
                    $table->date('payment_completed_date')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'interest_type')) {
                    $table->string('interest_type')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'interest_amount')) {
                    $table->decimal('interest_amount', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchase_invoice', 'final_amount_after_interest')) {
                    $table->decimal('final_amount_after_interest', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'extraction_metadata')) {
                    $table->longText('extraction_metadata')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'isDeleted')) {
                    $table->boolean('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('purchase_invoice', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('purchase_invoice', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: purchases
        if (!Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->string('item');
                $table->integer('quantity')->default(1);
                $table->string('batch_no')->nullable();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->decimal('mrp', 10, 2)->nullable()->default(0.00);
                $table->string('mfg_date')->nullable();
                $table->string('exp_date')->nullable();
                $table->integer('free_quantity')->nullable()->default(0);
                $table->longText('product_gst_details')->nullable();
                $table->decimal('product_gst_total', 10, 2)->nullable()->default(0.00);
                $table->decimal('price', 12, 2)->default(0.00);
                $table->integer('scheme_free_qty')->nullable()->default(0);
                $table->decimal('scheme_rate', 10, 2)->nullable()->default(0.00);
                $table->decimal('discount_percent', 10, 2)->nullable()->default(0.00);
                $table->decimal('discount_amount', 10, 2)->nullable()->default(0.00);
                $table->date('purchase_date')->nullable();
                $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('completed');
                $table->string('payment_status')->default('paid');
                $table->decimal('amount_total', 12, 2)->default(0.00);
                $table->string('purchase_type')->default('local');
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->string('invoice_id')->nullable();
                $table->bigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->longText('extraction_metadata')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('purchases', function (Blueprint $table) {
                if (!Schema::hasColumn('purchases', 'item')) {
                    $table->string('item');
                }
                if (!Schema::hasColumn('purchases', 'quantity')) {
                    $table->integer('quantity')->default(1);
                }
                if (!Schema::hasColumn('purchases', 'batch_no')) {
                    $table->string('batch_no')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'brand_id')) {
                    $table->unsignedBigInteger('brand_id')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'mrp')) {
                    $table->decimal('mrp', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'mfg_date')) {
                    $table->string('mfg_date')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'exp_date')) {
                    $table->string('exp_date')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'free_quantity')) {
                    $table->integer('free_quantity')->nullable()->default(0);
                }
                if (!Schema::hasColumn('purchases', 'product_gst_details')) {
                    $table->longText('product_gst_details')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'product_gst_total')) {
                    $table->decimal('product_gst_total', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'price')) {
                    $table->decimal('price', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'scheme_free_qty')) {
                    $table->integer('scheme_free_qty')->nullable()->default(0);
                }
                if (!Schema::hasColumn('purchases', 'scheme_rate')) {
                    $table->decimal('scheme_rate', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'discount_percent')) {
                    $table->decimal('discount_percent', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->nullable()->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'purchase_date')) {
                    $table->date('purchase_date')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'purchase_status')) {
                    $table->enum('purchase_status', ['pending', 'completed', 'cancelled'])->default('completed');
                }
                if (!Schema::hasColumn('purchases', 'payment_status')) {
                    $table->string('payment_status')->default('paid');
                }
                if (!Schema::hasColumn('purchases', 'amount_total')) {
                    $table->decimal('amount_total', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('purchases', 'purchase_type')) {
                    $table->string('purchase_type')->default('local');
                }
                if (!Schema::hasColumn('purchases', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'invoice_id')) {
                    $table->string('invoice_id')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'branch_id')) {
                    $table->bigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'extraction_metadata')) {
                    $table->longText('extraction_metadata')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'isDeleted')) {
                    $table->boolean('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('purchases', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('purchases', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: radiology_reports
        if (!Schema::hasTable('radiology_reports')) {
            Schema::create('radiology_reports', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id');
                $table->unsignedBigInteger('test_id');
                $table->date('report_date');
                $table->string('report_file');
                $table->string('converted_image')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('radiology_reports', function (Blueprint $table) {
                if (!Schema::hasColumn('radiology_reports', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('radiology_reports', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id');
                }
                if (!Schema::hasColumn('radiology_reports', 'test_id')) {
                    $table->unsignedBigInteger('test_id');
                }
                if (!Schema::hasColumn('radiology_reports', 'report_date')) {
                    $table->date('report_date');
                }
                if (!Schema::hasColumn('radiology_reports', 'report_file')) {
                    $table->string('report_file');
                }
                if (!Schema::hasColumn('radiology_reports', 'converted_image')) {
                    $table->string('converted_image')->nullable();
                }
                if (!Schema::hasColumn('radiology_reports', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('radiology_reports', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: radiology_tests
        if (!Schema::hasTable('radiology_tests')) {
            Schema::create('radiology_tests', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('test_name');
                $table->string('test_code');
                $table->string('body_part');
                $table->decimal('cost', 8, 2);
                $table->string('gst_option');
                $table->text('product_gst')->nullable();
                $table->string('report_format')->default('Digital Image');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['test_code'], 'radiology_tests_test_code_unique');
            });
        } else {
            Schema::table('radiology_tests', function (Blueprint $table) {
                if (!Schema::hasColumn('radiology_tests', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('radiology_tests', 'test_name')) {
                    $table->string('test_name');
                }
                if (!Schema::hasColumn('radiology_tests', 'test_code')) {
                    $table->string('test_code');
                }
                if (!Schema::hasColumn('radiology_tests', 'body_part')) {
                    $table->string('body_part');
                }
                if (!Schema::hasColumn('radiology_tests', 'cost')) {
                    $table->decimal('cost', 8, 2);
                }
                if (!Schema::hasColumn('radiology_tests', 'gst_option')) {
                    $table->string('gst_option');
                }
                if (!Schema::hasColumn('radiology_tests', 'product_gst')) {
                    $table->text('product_gst')->nullable();
                }
                if (!Schema::hasColumn('radiology_tests', 'report_format')) {
                    $table->string('report_format')->default('Digital Image');
                }
                if (!Schema::hasColumn('radiology_tests', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('radiology_tests', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: referal_doctors
        if (!Schema::hasTable('referal_doctors')) {
            Schema::create('referal_doctors', function (Blueprint $table) {
                $table->id();
                $table->string('doctor_name')->nullable();
                $table->string('specialist')->nullable();
                $table->string('email')->nullable();
                $table->string('phone_number')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('referal_doctors', function (Blueprint $table) {
                if (!Schema::hasColumn('referal_doctors', 'doctor_name')) {
                    $table->string('doctor_name')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'specialist')) {
                    $table->string('specialist')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'phone_number')) {
                    $table->string('phone_number')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('referal_doctors', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: role
        if (!Schema::hasTable('role')) {
            Schema::create('role', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['name'], 'role_name_unique');
            });
        } else {
            Schema::table('role', function (Blueprint $table) {
                if (!Schema::hasColumn('role', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('role', 'description')) {
                    $table->text('description');
                }
                if (!Schema::hasColumn('role', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('role', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: send_emails
        if (!Schema::hasTable('send_emails')) {
            Schema::create('send_emails', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('template_id');
                $table->string('sender_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('send_emails', function (Blueprint $table) {
                if (!Schema::hasColumn('send_emails', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('send_emails', 'template_id')) {
                    $table->unsignedBigInteger('template_id');
                }
                if (!Schema::hasColumn('send_emails', 'sender_id')) {
                    $table->string('sender_id');
                }
                if (!Schema::hasColumn('send_emails', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('send_emails', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: send_sms
        if (!Schema::hasTable('send_sms')) {
            Schema::create('send_sms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('template_id');
                $table->string('service');
                $table->string('sender_id');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('send_sms', function (Blueprint $table) {
                if (!Schema::hasColumn('send_sms', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('send_sms', 'template_id')) {
                    $table->unsignedBigInteger('template_id');
                }
                if (!Schema::hasColumn('send_sms', 'service')) {
                    $table->string('service');
                }
                if (!Schema::hasColumn('send_sms', 'sender_id')) {
                    $table->string('sender_id');
                }
                if (!Schema::hasColumn('send_sms', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('send_sms', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: services
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->string('department')->nullable();
                $table->unsignedBigInteger('service_id')->nullable();
                $table->text('description')->nullable();
                $table->decimal('cost', 10, 2)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('services', function (Blueprint $table) {
                if (!Schema::hasColumn('services', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('services', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('services', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('services', 'department')) {
                    $table->string('department')->nullable();
                }
                if (!Schema::hasColumn('services', 'service_id')) {
                    $table->unsignedBigInteger('service_id')->nullable();
                }
                if (!Schema::hasColumn('services', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('services', 'cost')) {
                    $table->decimal('cost', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('services', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('services', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: settings
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('key');
                $table->text('value');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->unique(['key'], 'settings_key_unique');
            });
        } else {
            Schema::table('settings', function (Blueprint $table) {
                if (!Schema::hasColumn('settings', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('settings', 'key')) {
                    $table->string('key');
                }
                if (!Schema::hasColumn('settings', 'value')) {
                    $table->text('value');
                }
                if (!Schema::hasColumn('settings', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('settings', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: sms_templates
        if (!Schema::hasTable('sms_templates')) {
            Schema::create('sms_templates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->enum('status', ['active', 'inactive']);
                $table->text('content');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('sms_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('sms_templates', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('sms_templates', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('sms_templates', 'status')) {
                    $table->enum('status', ['active', 'inactive']);
                }
                if (!Schema::hasColumn('sms_templates', 'content')) {
                    $table->text('content');
                }
                if (!Schema::hasColumn('sms_templates', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('sms_templates', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: soap
        if (!Schema::hasTable('soap')) {
            Schema::create('soap', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->date('date')->nullable();
                $table->longText('subjective')->nullable();
                $table->longText('objective')->nullable();
                $table->longText('assessment')->nullable();
                $table->longText('plan')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('soap', function (Blueprint $table) {
                if (!Schema::hasColumn('soap', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('soap', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('soap', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('soap', 'date')) {
                    $table->date('date')->nullable();
                }
                if (!Schema::hasColumn('soap', 'subjective')) {
                    $table->longText('subjective')->nullable();
                }
                if (!Schema::hasColumn('soap', 'objective')) {
                    $table->longText('objective')->nullable();
                }
                if (!Schema::hasColumn('soap', 'assessment')) {
                    $table->longText('assessment')->nullable();
                }
                if (!Schema::hasColumn('soap', 'plan')) {
                    $table->longText('plan')->nullable();
                }
                if (!Schema::hasColumn('soap', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('soap', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: stock_units
        if (!Schema::hasTable('stock_units')) {
            Schema::create('stock_units', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('stock_units', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_units', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('stock_units', 'short_name')) {
                    $table->string('short_name')->nullable();
                }
                if (!Schema::hasColumn('stock_units', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('stock_units', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('stock_units', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('stock_units', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('stock_units', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: suppliers
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->string('contact_person');
                $table->string('phone');
                $table->string('email')->nullable();
                $table->string('gst_number')->nullable();
                $table->string('drug_license_no')->nullable();
                $table->string('pan_no')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('account_no')->nullable();
                $table->string('ifsc_code')->nullable();
                $table->string('bank_branch')->nullable();
                $table->decimal('opening_balance', 12, 2)->default(0.00);
                $table->decimal('closing_balance', 12, 2)->default(0.00);
                $table->integer('balance_year')->nullable();
                $table->text('address');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('suppliers', function (Blueprint $table) {
                if (!Schema::hasColumn('suppliers', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('suppliers', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('suppliers', 'contact_person')) {
                    $table->string('contact_person');
                }
                if (!Schema::hasColumn('suppliers', 'phone')) {
                    $table->string('phone');
                }
                if (!Schema::hasColumn('suppliers', 'email')) {
                    $table->string('email')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'gst_number')) {
                    $table->string('gst_number')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'drug_license_no')) {
                    $table->string('drug_license_no')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'pan_no')) {
                    $table->string('pan_no')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'bank_name')) {
                    $table->string('bank_name')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'account_no')) {
                    $table->string('account_no')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'ifsc_code')) {
                    $table->string('ifsc_code')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'bank_branch')) {
                    $table->string('bank_branch')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'opening_balance')) {
                    $table->decimal('opening_balance', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('suppliers', 'closing_balance')) {
                    $table->decimal('closing_balance', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('suppliers', 'balance_year')) {
                    $table->integer('balance_year')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'address')) {
                    $table->text('address');
                }
                if (!Schema::hasColumn('suppliers', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('suppliers', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: symptoms
        if (!Schema::hasTable('symptoms')) {
            Schema::create('symptoms', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('name');
                $table->text('details')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('symptoms', function (Blueprint $table) {
                if (!Schema::hasColumn('symptoms', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('symptoms', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('symptoms', 'details')) {
                    $table->text('details')->nullable();
                }
                if (!Schema::hasColumn('symptoms', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('symptoms', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: taxes
        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->id();
                $table->string('tax_name')->nullable();
                $table->decimal('tax_rate', 12, 2)->default(0.00);
                $table->string('status')->default('active');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->boolean('isDeleted')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('taxes', function (Blueprint $table) {
                if (!Schema::hasColumn('taxes', 'tax_name')) {
                    $table->string('tax_name')->nullable();
                }
                if (!Schema::hasColumn('taxes', 'tax_rate')) {
                    $table->decimal('tax_rate', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('taxes', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('taxes', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('taxes', 'isDeleted')) {
                    $table->boolean('isDeleted')->default(0);
                }
                if (!Schema::hasColumn('taxes', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('taxes', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: therapy
        if (!Schema::hasTable('therapy')) {
            Schema::create('therapy', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('duration_minutes');
                $table->decimal('cost', 8, 2);
                $table->string('gst_option');
                $table->text('product_gst')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('therapy', function (Blueprint $table) {
                if (!Schema::hasColumn('therapy', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('therapy', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('therapy', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('therapy', 'duration_minutes')) {
                    $table->integer('duration_minutes');
                }
                if (!Schema::hasColumn('therapy', 'cost')) {
                    $table->decimal('cost', 8, 2);
                }
                if (!Schema::hasColumn('therapy', 'gst_option')) {
                    $table->string('gst_option');
                }
                if (!Schema::hasColumn('therapy', 'product_gst')) {
                    $table->text('product_gst')->nullable();
                }
                if (!Schema::hasColumn('therapy', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active');
                }
                if (!Schema::hasColumn('therapy', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('therapy', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: treatment_booking
        if (!Schema::hasTable('treatment_booking')) {
            Schema::create('treatment_booking', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('treatment_id')->nullable();
                $table->longText('machine_id')->nullable();
                $table->text('plan')->nullable();
                $table->decimal('remain_amount', 12, 2)->default(0.00);
                $table->string('status')->default('pending');
                $table->date('payment_date')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('treatment_booking', function (Blueprint $table) {
                if (!Schema::hasColumn('treatment_booking', 'patient_id')) {
                    $table->unsignedBigInteger('patient_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'treatment_id')) {
                    $table->unsignedBigInteger('treatment_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'machine_id')) {
                    $table->longText('machine_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'plan')) {
                    $table->text('plan')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'remain_amount')) {
                    $table->decimal('remain_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_booking', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('treatment_booking', 'payment_date')) {
                    $table->date('payment_date')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('treatment_booking', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: treatment_payment_history
        if (!Schema::hasTable('treatment_payment_history')) {
            Schema::create('treatment_payment_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('treatment_booking_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0.00);
                $table->string('payment_mode')->nullable();
                $table->string('paid_type')->nullable();
                $table->decimal('cash', 12, 2)->default(0.00);
                $table->decimal('online', 12, 2)->default(0.00);
                $table->decimal('paid_amount', 12, 2)->default(0.00);
                $table->decimal('remain_amount', 12, 2)->default(0.00);
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('treatment_payment_history', function (Blueprint $table) {
                if (!Schema::hasColumn('treatment_payment_history', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'treatment_booking_id')) {
                    $table->unsignedBigInteger('treatment_booking_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'amount')) {
                    $table->decimal('amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_payment_history', 'payment_mode')) {
                    $table->string('payment_mode')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'paid_type')) {
                    $table->string('paid_type')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'cash')) {
                    $table->decimal('cash', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_payment_history', 'online')) {
                    $table->decimal('online', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_payment_history', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_payment_history', 'remain_amount')) {
                    $table->decimal('remain_amount', 12, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatment_payment_history', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('treatment_payment_history', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: treatments
        if (!Schema::hasTable('treatments')) {
            Schema::create('treatments', function (Blueprint $table) {
                $table->id();
                $table->string('branch_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('doctor_id');
                $table->string('name');
                $table->decimal('price', 10, 2)->default(0.00);
                $table->string('gst_option');
                $table->text('product_gst')->nullable();
                $table->text('description')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('treatments', function (Blueprint $table) {
                if (!Schema::hasColumn('treatments', 'branch_id')) {
                    $table->string('branch_id')->nullable();
                }
                if (!Schema::hasColumn('treatments', 'user_id')) {
                    $table->unsignedBigInteger('user_id');
                }
                if (!Schema::hasColumn('treatments', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id');
                }
                if (!Schema::hasColumn('treatments', 'name')) {
                    $table->string('name');
                }
                if (!Schema::hasColumn('treatments', 'price')) {
                    $table->decimal('price', 10, 2)->default(0.00);
                }
                if (!Schema::hasColumn('treatments', 'gst_option')) {
                    $table->string('gst_option');
                }
                if (!Schema::hasColumn('treatments', 'product_gst')) {
                    $table->text('product_gst')->nullable();
                }
                if (!Schema::hasColumn('treatments', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('treatments', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('treatments', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: user
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id();
                $table->string('plan_id', 200)->nullable();
                $table->unsignedBigInteger('role_id')->nullable();
                $table->string('username', 100)->nullable();
                $table->text('fullname')->nullable();
                $table->text('email')->nullable();
                $table->text('phone')->nullable();
                $table->text('profile')->nullable();
                $table->text('password')->nullable();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('clinic_name')->nullable();
                $table->string('clinic_logo')->nullable();
                $table->string('reset_token')->nullable();
                $table->string('status')->default('active');
                $table->text('fcm_token')->nullable();
                $table->string('forget_pass_key')->nullable();
                $table->boolean('is_deleted')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('user', function (Blueprint $table) {
                if (!Schema::hasColumn('user', 'plan_id')) {
                    $table->string('plan_id', 200)->nullable();
                }
                if (!Schema::hasColumn('user', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->nullable();
                }
                if (!Schema::hasColumn('user', 'username')) {
                    $table->string('username', 100)->nullable();
                }
                if (!Schema::hasColumn('user', 'fullname')) {
                    $table->text('fullname')->nullable();
                }
                if (!Schema::hasColumn('user', 'email')) {
                    $table->text('email')->nullable();
                }
                if (!Schema::hasColumn('user', 'phone')) {
                    $table->text('phone')->nullable();
                }
                if (!Schema::hasColumn('user', 'profile')) {
                    $table->text('profile')->nullable();
                }
                if (!Schema::hasColumn('user', 'password')) {
                    $table->text('password')->nullable();
                }
                if (!Schema::hasColumn('user', 'branch_id')) {
                    $table->unsignedBigInteger('branch_id')->nullable();
                }
                if (!Schema::hasColumn('user', 'clinic_name')) {
                    $table->string('clinic_name')->nullable();
                }
                if (!Schema::hasColumn('user', 'clinic_logo')) {
                    $table->string('clinic_logo')->nullable();
                }
                if (!Schema::hasColumn('user', 'reset_token')) {
                    $table->string('reset_token')->nullable();
                }
                if (!Schema::hasColumn('user', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('user', 'fcm_token')) {
                    $table->text('fcm_token')->nullable();
                }
                if (!Schema::hasColumn('user', 'forget_pass_key')) {
                    $table->string('forget_pass_key')->nullable();
                }
                if (!Schema::hasColumn('user', 'is_deleted')) {
                    $table->boolean('is_deleted')->default(0);
                }
                if (!Schema::hasColumn('user', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('user', 'remember_token')) {
                    $table->string('remember_token', 100)->nullable();
                }
                if (!Schema::hasColumn('user', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('user', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: user_details
        if (!Schema::hasTable('user_details')) {
            Schema::create('user_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('address')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->date('birth_date')->nullable();
                $table->text('education')->nullable();
                $table->text('experience')->nullable();
                $table->string('shift')->nullable();
                $table->decimal('salary', 10, 2)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('user_details', function (Blueprint $table) {
                if (!Schema::hasColumn('user_details', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'address')) {
                    $table->string('address')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'state')) {
                    $table->string('state')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'gender')) {
                    $table->enum('gender', ['male', 'female', 'other'])->nullable();
                }
                if (!Schema::hasColumn('user_details', 'birth_date')) {
                    $table->date('birth_date')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'education')) {
                    $table->text('education')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'experience')) {
                    $table->text('experience')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'shift')) {
                    $table->string('shift')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'salary')) {
                    $table->decimal('salary', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('user_details', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('user_details', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        // Table: user_permissions
        if (!Schema::hasTable('user_permissions')) {
            Schema::create('user_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('module_id')->nullable();
                $table->boolean('create')->default(0);
                $table->boolean('view')->default(0);
                $table->boolean('update')->default(0);
                $table->boolean('delete')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('user_permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('user_permissions', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable();
                }
                if (!Schema::hasColumn('user_permissions', 'module_id')) {
                    $table->unsignedBigInteger('module_id')->nullable();
                }
                if (!Schema::hasColumn('user_permissions', 'create')) {
                    $table->boolean('create')->default(0);
                }
                if (!Schema::hasColumn('user_permissions', 'view')) {
                    $table->boolean('view')->default(0);
                }
                if (!Schema::hasColumn('user_permissions', 'update')) {
                    $table->boolean('update')->default(0);
                }
                if (!Schema::hasColumn('user_permissions', 'delete')) {
                    $table->boolean('delete')->default(0);
                }
                if (!Schema::hasColumn('user_permissions', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('user_permissions', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('user_details');
        Schema::dropIfExists('user');
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('treatment_payment_history');
        Schema::dropIfExists('treatment_booking');
        Schema::dropIfExists('therapy');
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('symptoms');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('stock_units');
        Schema::dropIfExists('soap');
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('services');
        Schema::dropIfExists('send_sms');
        Schema::dropIfExists('send_emails');
        Schema::dropIfExists('role');
        Schema::dropIfExists('referal_doctors');
        Schema::dropIfExists('radiology_tests');
        Schema::dropIfExists('radiology_reports');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_invoice');
        Schema::dropIfExists('project_types');
        Schema::dropIfExists('product_documentations');
        Schema::dropIfExists('product_barcodes');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('payment_store');
        Schema::dropIfExists('payment_history');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('patient_medicine');
        Schema::dropIfExists('patient_discharge_dets');
        Schema::dropIfExists('patient_diagnosis');
        Schema::dropIfExists('patient_assign_homeadvice');
        Schema::dropIfExists('patient_assign_dietchart');
        Schema::dropIfExists('patient_assign_assessment');
        Schema::dropIfExists('pathology_tests');
        Schema::dropIfExists('pathology_reports');
        Schema::dropIfExists('ot_procedures');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_patient');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('opd_visits');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('medicine_units');
        Schema::dropIfExists('medical_reports');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('ipd_admissions');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('home_advice');
        Schema::dropIfExists('followups');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('email_template');
        Schema::dropIfExists('diet_charts');
        Schema::dropIfExists('diagnosis');
        Schema::dropIfExists('default_email_template');
        Schema::dropIfExists('daily_data');
        Schema::dropIfExists('code_master');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('chat_history');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('bank_master');
        Schema::dropIfExists('assigned_therapies');
        Schema::dropIfExists('assessment_template');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('appointment_history');
        Schema::dropIfExists('ai_credentials');
    }
};
