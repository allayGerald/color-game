<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Color game</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        [x-cloak] {
            display: none;
        }
        .progress {
            animation:progress;
            animation-iteration-count:1;
            animation-fill-mode:forwards;
            animation-timing-function:linear;
        }

        @keyframes progress {
            0% {
                width: 0%;
            }
            80% {
                opacity: 1;
            }
            100% {
                opacity: 1;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="pb-4 flex flex-col items-center min-h-screen" x-data="initGame()" x-init="changeColors">
    <button type="button" @click="restart()"
            x-bind:disabled="playing"
            :class="{'bg-gray-100 hover:bg-green-100' : playing}"
            class="px-6 py-2 my-6 bg-green-500 hover:bg-green-600 text-2xl text-white font-semibold uppercase rounded-md">
        play
    </button>

    <div
            x-cloak
            class="relative w-full bg-gray-200">
        <div
                class="absolute inset-0 bg-red-500 h-2 w-0"
                :class="{'progress': playing}"
                :style="`animation-duration:${timerLimit * 1000}ms;`">
        </div>
    </div>

    <h1 class="pt-6 mb-4 text-6xl font-semibold"
        x-text="displayTimer"
        :class="'text-' + randomColor() + '-500'"
    >10</h1>

    <div class="absolute right-0 top-0 m-5 w-1/2 xl:w-1/5 lg:w-1/4 md:w-2/5 sm:w-1/2" >
            <div
                    x-show="showToast"
                    x-transition:enter="transition ease-in duration-200"
                    x-transition:enter-start="transform opacity-0 translate-y-2"
                    x-transition:enter-end="transform opacity-100"
                    x-transition:leave="transition ease-out duration-500"
                    x-transition:leave-start="transform translate-x-0 opacity-100"
                    x-transition:leave-end="transform translate-x-full opacity-0"
                    class="bg-red-500 border-red-700 py-2 px-3 shadow-md mb-2 border-r-4"
                    >

                <div class="flex justify-between">
                    <div class="text-white">
                        <div class="text-base" x-text="`Game over! Your Score ${displayScore}`"></div>

                        <div class="flex space-x-2">
                            <button class="mt-4 px-4 py-2 bg-green-500 hover:bg-green-600 text-base text-white font-semibold uppercase rounded-md"
                            @click="restart()">
                                New Game
                            </button>
                            <button class="mt-4 px-4 py-2 bg-red-500 hover:bg-red-600 text-base text-white font-semibold uppercase rounded-md"
                                    @click="showToast = false">
                                cancel
                            </button>
                        </div>
                    </div>

                    <button class='text-red-500 rounded-full bg-white h-5 w-5 flex items-center justify-center'
                     @click="showToast = false">
                        <svg class="w-4 h-4 bi bi-x" viewBox='0 0 16 16' fill='currentColor'
                             xmlns='http://www.w3.org/2000/svg'>
                            <path fill-rule='evenodd'
                                  d='M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z'/>
                            <path fill-rule='evenodd'
                                  d='M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z'/>
                        </svg>
                    </button>
                </div>
            </div>
    </div>


    <div class="mb-4 flex-1 flex flex-wrap items-center justify-center w-1/3"

    >
        <template x-for="(card, index) in cards" :key="index">
            <button
                    type="button"
                    x-text="card.displayColor"
                    class="flex items-center justify-center w-24 h-24 text-white uppercase tracking-wide font-semibold"
                    :class="[ `bg-${card.actualColor}-500 ${levelAnimation}` ]"
                    @click="select(card)"
            ></button>
        </template>
    </div>
    <h1 class="mb-4 text-indingo-500 text-2xl font-semibold" x-text="'Score: ' + displayScore"></h1>
    <p class="mb-4 text-indingo-500 text-2xl font-semibold" x-text="'Level: ' + level + '/5' "></p>
</div>

<script defer src="https://unpkg.com/alpinejs@3.2.3/dist/cdn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/collect.js/4.28.7/collect.min.js"
        integrity="sha512-dtpw7xnweOwB90yc1FsfiZQVBsQ8UPCrdFE6Z13SEEth8EAAeCPv5XTe6Q2P1K0O8SielXw0843qyA2fE0ioQQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  function initGame () {
    return {
      intervalId: null,
      timerLimit: 10,
      timer: 1,
      showToast: false,
      colors: ['gray', 'red', 'yellow', 'green', 'blue', 'indigo', 'purple', 'pink'],
      cards: [],
      cardQuantity: 25,
      playing: false,
      score: 0,

      changeColors () {
        let color = this.randomColor()
        let correctColor = {
          actualColor: color,
          displayColor: color
        }

        this.cards = collect()
          .times((this.cardQuantity - 1), () => {
            return {
              actualColor: this.randomColor(),
              displayColor: this.randomColor()
            }
          })
          .push(correctColor)
          .shuffle()
          .all()
        console.log(this.cards)
      },

      randomColor () {
        return collect(this.colors).random()
      },

      start () {
        this.playing = true
        this.score = 0

        this.changeColors()

        this.timer = 1
        this.intervalId = setInterval(() => {
          (this.timer === this.timerLimit) ? this.gameOver() : this.timer++
        }, 1000)

        console.log(this.timer, this.timerLimit)
      },

      get displayTimer () {
        return this.timerLimit - this.timer + 1
      },

      get displayScore () {
        return this.score * 10
      },

      get level () {
        if (this.score >= 5) {
          return 5
        }

        return this.score
      },

      select(card) {
        console.log(card)
        if (! this.playing) {
          console.log('not')
          return
        }
        console.log('hapa')
        if (card.displayColor === card.actualColor) {
          console.log('correct')
          this.timer = 1
          this.score++
          this.changeColors()
        }
      },

      get levelAnimation() {
        switch (+this.level) {
          case 0:
            return ''
          case 1:
            return 'animate-pulse'
          case 2:
            return 'animate-bounce'
          case 3:
            return 'animate-spin'
          default:
            return 'animate-ping'
        }
      },

      gameOver () {
        this.playing = false
        clearInterval(this.intervalId)
       /* if (confirm(`Game Over. Your score is ${this.displayScore}. Want to restart?`)) {
          this.start()
        }*/

        this.showToast = true
      },

      restart() {
        this.showToast = false
        this.start()
      }
    }
  }

  //high score
  //pop up
  //animate countdown slider
  //animate color change
</script>
</body>
</html>