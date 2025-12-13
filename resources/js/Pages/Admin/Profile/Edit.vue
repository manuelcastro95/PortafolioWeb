<template>
    <AdminLayout>
        <Head title="Editar Perfil" />
        
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-dark mb-2">Editar Perfil</h1>
                    <p class="text-gray-600">Actualiza tu información personal y profesional</p>
                </div>

                <!-- Success Message -->
                <div 
                    v-if="$page.props.flash?.success" 
                    class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ $page.props.flash.success }}
                    </div>
                </div>

                <!-- Error Messages -->
                <div 
                    v-if="$page.props.flash?.error" 
                    class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        {{ $page.props.flash.error }}
                    </div>
                </div>

                <div 
                    v-if="form.errors && Object.keys(form.errors).length > 0" 
                    class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md"
                >
                    <div class="font-semibold mb-2">Errores de validación:</div>
                    <ul class="list-disc list-inside space-y-1">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <!-- Main Form Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Personal Information -->
                            <div>
                                <h2 class="text-xl font-bold text-dark mb-6 pb-3 border-b border-gray-200">
                                    Información Personal
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Nombre <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="Tu nombre completo"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="tu@email.com"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Biografía</label>
                                <textarea
                                    v-model="form.bio"
                                    rows="4"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                        resize-none
                                    "
                                    placeholder="Escribe una breve descripción sobre ti..."
                                ></textarea>
                            </div>

                            <!-- Contact Information -->
                            <div>
                                <h2 class="text-xl font-bold text-dark mb-6 pb-3 border-b border-gray-200">
                                    Información de Contacto
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                                        <input
                                            v-model="form.phone"
                                            type="text"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="+1234567890"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp</label>
                                        <input
                                            v-model="form.whatsapp"
                                            type="text"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="+1234567890"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Social Links -->
                            <div>
                                <h2 class="text-xl font-bold text-dark mb-6 pb-3 border-b border-gray-200">
                                    Redes Sociales
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">LinkedIn URL</label>
                                        <input
                                            v-model="form.linkedin_url"
                                            type="url"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="https://linkedin.com/in/tu-perfil"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">GitHub URL</label>
                                        <input
                                            v-model="form.github_url"
                                            type="url"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                            "
                                            placeholder="https://github.com/tu-usuario"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Files Section -->
                            <div>
                                <h2 class="text-xl font-bold text-dark mb-6 pb-3 border-b border-gray-200">
                                    Archivos
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Profile Image -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Imagen de Perfil</label>
                                        <div v-if="user?.profile_image_url || user?.profile_image" class="mb-4">
                                            <div class="relative inline-block">
                                                <img
                                                    :src="user?.profile_image_url || getImageUrl(user?.profile_image)"
                                                    alt="Perfil"
                                                    class="h-32 w-32 object-cover rounded-2xl shadow-lg border-4 border-white"
                                                />
                                                <div class="absolute inset-0 bg-black/0 hover:bg-black/10 rounded-2xl transition-colors duration-200 flex items-center justify-center">
                                                    <span class="text-white opacity-0 hover:opacity-100 text-xs font-medium">Vista previa</span>
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            @change="handleProfileImageChange"
                                            type="file"
                                            accept="image/*"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                                file:mr-4 file:py-2 file:px-4 file:rounded-lg
                                                file:border-0 file:text-sm file:font-semibold
                                                file:bg-primary file:text-white
                                                hover:file:bg-primary/90
                                                cursor-pointer
                                            "
                                        />
                                        <p class="text-xs text-gray-500 mt-2">Formatos: JPG, PNG, GIF. Máx: 2MB</p>
                                    </div>

                                    <!-- CV File -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">CV (PDF)</label>
                                        <div v-if="user?.cv_file_url || user?.cv_file" class="mb-4">
                                            <div class="
                                                p-4 rounded-xl border-2 border-dashed border-gray-300
                                                bg-gray-50 hover:border-primary/50 transition-colors
                                            ">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900">CV actual</p>
                                                        <button
                                                            type="button"
                                                            @click="showCvModal = true"
                                                            class="text-sm text-primary hover:text-primary/80 font-medium mt-1"
                                                        >
                                                            Ver CV
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            @change="handleCvChange"
                                            type="file"
                                            accept=".pdf"
                                            class="
                                                w-full px-4 py-3 rounded-xl
                                                border border-gray-300
                                                focus:border-primary focus:ring-2 focus:ring-primary/20
                                                transition-all duration-200
                                                file:mr-4 file:py-2 file:px-4 file:rounded-lg
                                                file:border-0 file:text-sm file:font-semibold
                                                file:bg-primary file:text-white
                                                hover:file:bg-primary/90
                                                cursor-pointer
                                            "
                                        />
                                        <p class="text-xs text-gray-500 mt-2">Formato: PDF. Máx: 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="
                                        px-8 py-3 rounded-xl
                                        bg-primary text-white
                                        hover:bg-primary/90 shadow-lg hover:shadow-xl
                                        disabled:opacity-50 disabled:cursor-not-allowed
                                        transition-all duration-200
                                        font-semibold
                                        flex items-center gap-2
                                    "
                                >
                                    <svg v-if="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Modal -->
        <CvModal
            v-if="user?.cv_file || user?.cv_file_url"
            :is-open="showCvModal"
            :cv-url="getCvUrl()"
            @close="showCvModal = false"
        />
    </AdminLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CvModal from '@/Components/Portfolio/CvModal.vue';

const showCvModal = ref(false);

const props = defineProps({
    user: Object,
});

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    bio: props.user?.bio || '',
    phone: props.user?.phone || '',
    linkedin_url: props.user?.linkedin_url || '',
    github_url: props.user?.github_url || '',
    whatsapp: props.user?.whatsapp || '',
    profile_image: null,
    cv_file: null,
});

const processing = computed(() => form.processing);

const handleProfileImageChange = (event) => {
    if (event.target.files && event.target.files[0]) {
        form.profile_image = event.target.files[0];
    }
};

const handleCvChange = (event) => {
    if (event.target.files && event.target.files[0]) {
        form.cv_file = event.target.files[0];
    }
};

const getImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    if (props.user.profile_image_url) {
        return props.user.profile_image_url;
    }
    const baseUrl = import.meta.env.VITE_APP_URL || '';
    if (import.meta.env.VITE_AWS_URL) {
        return `${import.meta.env.VITE_AWS_URL}/${path}`;
    }
    return `${baseUrl}/storage/${path}`;
};

const getCvUrl = () => {
    if (!props.user.cv_file) return '#';
    if (props.user.cv_file_url) {
        return props.user.cv_file_url;
    }
    if (props.user.cv_file.startsWith('http')) {
        return props.user.cv_file;
    }
    const baseUrl = import.meta.env.VITE_APP_URL || '';
    if (import.meta.env.VITE_AWS_URL) {
        return `${import.meta.env.VITE_AWS_URL}/${props.user.cv_file}`;
    }
    return `${baseUrl}/storage/${props.user.cv_file}`;
};

const submit = () => {
    if (!form.name || form.name.trim() === '') {
        form.setError('name', 'El nombre es requerido');
        return;
    }
    
    if (!form.email || form.email.trim() === '') {
        form.setError('email', 'El email es requerido');
        return;
    }
    
    form.clearErrors();
    
    form.name = String(form.name || '').trim();
    form.email = String(form.email || '').trim();
    form.bio = form.bio || null;
    form.phone = form.phone || null;
    form.linkedin_url = form.linkedin_url || null;
    form.github_url = form.github_url || null;
    form.whatsapp = form.whatsapp || null;
    
    const hasFiles = form.profile_image || form.cv_file;
    
    const options = {
        onSuccess: () => {
            window.location.reload();
        },
        onError: (errors) => {
            console.error('Errores al actualizar:', errors);
        },
    };
    
    if (hasFiles) {
        form._method = 'PATCH';
        form.post(route('admin.profile.update'), {
            forceFormData: true,
            ...options,
        });
    } else {
        form.patch(route('admin.profile.update'), options);
    }
};
</script>
