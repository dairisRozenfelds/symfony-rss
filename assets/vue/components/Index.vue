<template>
    <div class="index-wrapper pt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7">
                <h1 class="display-4">RSS Feed reader</h1>
                <div class="logout-wrapper">
                    <a href="/logout">Logout</a>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-md-7 common-words-wrapper">
                <h2 class="mb-3 mt-3">The most common words in the entry summaries</h2>
                <div class="common-word" v-for="commonWord in mostCommonWordsList">
                    <small class="word">{{ commonWord.word }}</small><small class="count">{{ commonWord.word_count }}</small>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12 col-md-7 entry-wrapper">
                <h2 class="mb-3 mt-3">RSS Entry list</h2>
                <div class="entry" v-for="entry in entries">
                    <div class="entry-title">
                        <a :href="entry.link"><span>{{ entry.title }}</span></a>
                    </div>
                    <div class="entry-subtitle d-lg-flex">
                        <div class="entry-subtitle-left">
                            <span class="entry-id">{{ entry.id }}</span>
                        </div>
                        <div class="entry-subtitle-right">
                            <span class="entry-author-name">{{ entry.author_name }}</span>
                            <span class="entry-updated-at">{{ entry.updated_at }}</span>
                        </div>
                    </div>
                    <div class="entry-content">
                        <p class="entry-content-summary">{{ entry.content_summary }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Index",
        props: {
            rssData: {
                required: true
            }
        },

        computed: {
            entries() {
                return this.rssData.entries;
            },

            mostCommonWordsList() {
                return this.rssData.common_words;
            }
        },
    }
</script>
