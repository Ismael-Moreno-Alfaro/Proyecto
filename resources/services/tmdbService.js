const tmdbService = {

    apiKey: '2c04311500dcef408f8bc9b4ed88e4ea',
    baseURL: 'https://api.themoviedb.org/3',
    language: 'language=es-Es',
    region: 'region=ES',

    async getMovies() {
        try {
            const response = await fetch(`${this.baseURL}/discover/movie?api_key=${this.apiKey}&${this.language}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            const movies = data.results;

            return movies;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
    },

    async getMoviesScroll(page = 1) {
        try {
            const response = await fetch(`${this.baseURL}/discover/movie?api_key=${this.apiKey}&page=${page}&${this.language}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
    },

    async getTopRatedMoviesScroll(page = 1) {
        try {
            const response = await fetch(`${this.baseURL}/movie/top_rated?api_key=${this.apiKey}&page=${page}&${this.language}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
    },

    async getDirector(id) {
        const creditsUrl = `${this.baseURL}/movie/${id}/credits?api_key=${this.apiKey}`;
        const response = await fetch(creditsUrl);
        const creditsData = await response.json();
        const director = creditsData.crew.find(crewMember => crewMember.job === 'Director');
        return director ? director.name : 'Director not found';
    },

    async getMovieDetails(id) {
        const response = await fetch(`${this.baseURL}/movie/${id}?api_key=${this.apiKey}&${this.language}`);
        return await response.json();
    },

    async getMovieCast(id) {
        const response = await fetch(`${this.baseURL}/movie/${id}/credits?api_key=${this.apiKey}&${this.language}`);
        const data = await response.json();
        return data.cast;
    },

    async getMovieReleaseDates(id) {
        try {
            const response = await fetch(`${this.baseURL}/movie/${id}/release_dates?api_key=${this.apiKey}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data.results;
        } catch (error) {
            console.error('Error fetching movie release dates from TMDB:', error);
            throw error;
        }
    },

    async getFilmography(actorId) {
        try {
            const response = await fetch(`${this.baseURL}/person/${actorId}/movie_credits?api_key=${this.apiKey}&${this.language}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            const filmography = data.cast;

            return filmography;
        } catch (error) {
            console.error('Error fetching filmography from TMDB:', error);
            throw error;
        }
    },

    async getActorData(actorId) {
        try {
            const response = await fetch(`${this.baseURL}/person/${actorId}?api_key=${this.apiKey}&${this.language}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const actorData = await response.json();


            return actorData;
        } catch (error) {
            console.error('Error fetching actor data from TMDB:', error);
            throw error;
        }
    },

    async getNowPlayingMoviesSpain(page = 1) {
        try {
            const response = await fetch(`${this.baseURL}/movie/now_playing?api_key=${this.apiKey}&${this.language}&page=${page}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
    },

    async getUpcomingMoviesSpain(page = 1) {
        try {
            const response = await fetch(`${this.baseURL}/movie/upcoming?api_key=${this.apiKey}&${this.language}&${this.region}&page=${page}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
    },

    async searchMovies(query, page = 1) {
        try {
            const response = await fetch(`${this.baseURL}/search/movie?api_key=${this.apiKey}&${this.language}&query=${query}&page=${page}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching movies from TMDB:', error);
            throw error;
        }
      },
};

export default tmdbService;
