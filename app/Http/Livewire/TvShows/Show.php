<?php

namespace App\Http\Livewire\TvShows;

use App\Http\Livewire\BaseComponent;
use App\Repositories\TvShowRepository;
use App\Repositories\EpisodeRepository;
use App\Repositories\CastRepository;
use App\Models\TvShowView;

class Show extends BaseComponent
{

    public $tvShow, $seasons = [], $season_number = 1, $episodes = [];

    public $selectedEpisode, $casts = [];

    public function mount($slug){

        $this->fill([
            'tvShow' => TvShowRepository::getTvShowBySlug($slug)
        ]);

        if(request()->has(['season', 'episode'])){
            $this->fill([
                'selectedEpisode' => EpisodeRepository::getEpisodeBySlug(request('episode'), $this->tvShow->id),
                'season_number' => request('season')
            ]);
        }

        $this->fill([
            'seasons' => TvShowRepository::getTvShowSeasons($this->tvShow->id),
            'episodes' => EpisodeRepository::getEpisodesBySeason($this->tvShow->id, $this->season_number),
            'casts' => CastRepository::getCastsByShow($this->tvShow->id)
        ]);

        $data = [
            'user_id' => auth()->id(),
            'tv_show_id' => $this->tvShow->id,
            'episode_id' => $this->selectedEpisode->id ?? NULL,
            'ip_address' => request()->ip()
        ];

        TvShowView::firstOrCreate($data, $data);

        
    }

    public function updatedSeasonNumber($value){
        $this->episodes = EpisodeRepository::getEpisodesBySeason($this->tvShow->id, $this->season_number);
    }

    public function selectEpisode($episodeId){
        $this->selectedEpisode = EpisodeRepository::getEpisodeById($episodeId);

        $this->dispatchBrowserEvent("change-episode", [
            'video_url' => file_path($this->selectedEpisode->recorded_video),
            'episode' => $this->selectedEpisode->slug,
            'season' => $this->selectedEpisode->season_number
        ]);
    }

    public function render()
    {
        return view('livewire.tv-shows.show');
    }
}
