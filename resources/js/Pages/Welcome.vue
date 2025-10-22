<template>
  <AppLayout>
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-950">
      <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32">
        <div class="text-center">
          <h1 class="text-4xl sm:text-6xl font-extrabold text-white mb-6 tracking-tight">
            Créez du contenu IA en
            <span class="bg-gradient-to-r from-cyan-400 to-blue-300 bg-clip-text text-transparent">
              quelques secondes
            </span>
          </h1>
          <p class="text-xl sm:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
            La plateforme tout-en-un pour générer du texte et des images avec l'intelligence artificielle
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <Link 
              v-if="!$page.props.auth.user"
              :href="route('register')"
              class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1"
            >
              Commencer gratuitement
              <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </Link>
            <Link 
              v-if="!$page.props.auth.user"
              :href="route('login')"
              class="inline-flex items-center px-8 py-4 bg-blue-500/20 text-white rounded-lg font-semibold text-lg hover:bg-blue-500/30 transition-all duration-200 backdrop-blur-sm border border-white/20"
            >
              Se connecter
            </Link>
            <Link 
              v-if="$page.props.auth.user"
              :href="route('dashboard')"
              class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all duration-200 shadow-xl"
            >
              Accéder au tableau de bord
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div class="py-24 bg-white dark:bg-gray-900">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Fonctionnalités puissantes
          </h2>
          <p class="text-xl text-gray-600 dark:text-gray-400">
            Tout ce dont vous avez besoin pour créer du contenu IA de qualité
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Génération de texte</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Créez des articles, descriptions de produits, posts sociaux et plus avec GPT-4
            </p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Génération d'images</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Créez des images uniques et professionnelles avec DALL-E 3
            </p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">API REST</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Intégrez facilement l'IA dans vos applications avec notre API
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pricing Section -->
    <div class="py-24 bg-gray-50 dark:bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Tarifs simples et transparents
          </h2>
          <p class="text-xl text-gray-600 dark:text-gray-400">
            Choisissez le plan qui correspond à vos besoins
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Free Plan -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-8 border-2 border-gray-200 dark:border-gray-600">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Free</h3>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900 dark:text-white">0€</span>
              <span class="text-gray-600 dark:text-gray-400">/mois</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                1,000 crédits texte/mois
              </li>
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                10 crédits image/mois
              </li>
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Accès aux templates
              </li>
            </ul>
            <Link 
              :href="route('register')"
              class="block w-full text-center px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition"
            >
              Commencer
            </Link>
          </div>

          <!-- Pro Plan -->
          <div class="bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-2xl p-8 border-2 border-blue-500 transform scale-105">
            <div class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full inline-block mb-4">
              POPULAIRE
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Pro</h3>
            <div class="mb-6">
              <span class="text-4xl font-bold text-white">29€</span>
              <span class="text-blue-100">/mois</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-white">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                50,000 crédits texte/mois
              </li>
              <li class="flex items-center text-white">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                100 crédits image/mois
              </li>
              <li class="flex items-center text-white">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Accès API
              </li>
              <li class="flex items-center text-white">
                <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Support prioritaire
              </li>
            </ul>
            <Link 
              :href="route('register')"
              class="block w-full text-center px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg"
            >
              Commencer
            </Link>
          </div>

          <!-- Team Plan -->
          <div class="bg-white dark:bg-gray-700 rounded-2xl shadow-lg p-8 border-2 border-gray-200 dark:border-gray-600">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Team</h3>
            <div class="mb-6">
              <span class="text-4xl font-bold text-gray-900 dark:text-white">99€</span>
              <span class="text-gray-600 dark:text-gray-400">/mois</span>
            </div>
            <ul class="space-y-3 mb-8">
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                200,000 crédits texte/mois
              </li>
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                500 crédits image/mois
              </li>
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Gestion d'équipe
              </li>
              <li class="flex items-center text-gray-600 dark:text-gray-300">
                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Support dédié
              </li>
            </ul>
            <Link 
              :href="route('register')"
              class="block w-full text-center px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold hover:bg-gray-300 dark:hover:bg-gray-500 transition"
            >
              Commencer
            </Link>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-900 dark:to-indigo-900 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
          Prêt à créer du contenu IA ?
        </h2>
        <p class="text-xl text-blue-100 mb-8">
          Rejoignez des milliers d'utilisateurs qui font confiance à notre plateforme
        </p>
        <Link 
          v-if="!$page.props.auth.user"
          :href="route('register')"
          class="inline-flex items-center px-8 py-4 bg-white text-blue-600 rounded-lg font-semibold text-lg hover:bg-blue-50 transition-all duration-200 shadow-xl"
        >
          Commencer gratuitement
          <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
</script>
