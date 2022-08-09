<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .carousel{ 
  $scrollbarHeight: 4px;
  $scrollbarColor : #D82B6A;
  $carouselHeight : 200px;
  $fadeWidth      : 50px;
  $padding        : 8px;
  $radius         : 8px;         
  $bg             : white;
  
  display: block; 
  font-size: 0;
  border-radius: $radius;
  padding: $padding;
  box-shadow: 0 4px 10px rgba(black, .15);
  background: $bg;
  transform: translateZ(0);  
  height: $carouselHeight;
  -webkit-overflow-scrolling:touch;

  .touch &{ overflow:auto; } /* for tablets */

  &[data-at*='left'] > .wrap{
      &::before{ 
        opacity:1;
        text-indent:-50px; 
    }
  }
  
  &[data-at*='right'] > .wrap{
      &::after{ 
        opacity:1;
        text-indent:-50px; 
    }
  }

  /*/ scrollbar*/
  &::after{  
    content: '';
    pointer-events:none;
    position:absolute; 
    z-index: 4;
    bottom: -4px; 
    left: 0;
    background: $scrollbarColor;
    height: $scrollbarHeight;
    border-radius: $scrollbarHeight;
    opacity: 0;
    width: var(--scrollWidth, 0);
    left: var(--scrollLleft, 0);
    transition: opacity .2s, bottom .2s;
  }

  &:hover{  
    &::after{ opacity:1; bottom:-10px; }
  }

  > .wrap{ 
    overflow:hidden;
    border-radius: $radius - $padding/2;
    
    &::before, 
    &::after{ 
      content: '\2039'; 
      opacity: 0; 
      position: absolute; 
      top: 0;
      left: 0;
      bottom: 0;
      z-index: 2;
      width: $fadeWidth;
      font-size: 80px;
      text-indent:-30px;
      line-height: $carouselHeight;
      font-family: monospace;
      color: #555;
      font-weight: bold;
      border-radius: $radius;
      pointer-events: none;
      transition: .2s ease-out; 
      background:linear-gradient(to right, white 20%, transparent); 
    }

    &::after{ 
      transform:rotate(180deg);
      left: auto;
      right:0; 
    }

    > ul{ 
      list-style:none; 
      white-space:nowrap; 
      height:$carouselHeight;

      > li{ 
          display:inline-block; 
          vertical-align:middle; 
          height:100%; 
          margin:0 0 0 5px; 
          position:relative; 
          overflow:hidden; 
          transition:0.25s ease-out;
          &:first-child{ margin:0; }

          > img{ 
             display:block; 
             height:100%; 
             margin:auto; 
             vertical-align:bottom; 
             position:relative; 
             z-index:1; 
             transition:1s ease; 
          }
          /*/&:hover img{ transform:scale(1.1); }*/
        }
     }
  }
}

body{ background:#EEE; }

/*demo only*/
.carousel{ 
  position: absolute;
  top:0; 
  right:0; 
  bottom:0; 
  left:0;
  width: 50%; 
  max-width: 900px;
  min-width: 550px;
  margin: auto;
}
</style>
<script>
// Hover-Carousel component
// By Yair Even-Or
// written in jQuery 2013 -> refactored to vanilla 2020
// https://github.com/yairEO/hover-carousel

function HoverCarousel( elm, settings ){
  this.DOM = {
    scope: elm,
    wrap: elm.querySelector('ul').parentNode
  }
  
  this.containerWidth = 0;
  this.scrollWidth = 0;
  this.posFromLeft = 0;    // Stripe position from the left of the screen
  this.stripePos = 0;    // When relative mouse position inside the thumbs stripe
  this.animated = null;
  this.callbacks = {}
  
  this.init()
}

HoverCarousel.prototype = {
  init(){
    this.bind()
    },
    
    destroy(){
        this.DOM.scope.removeEventListener('mouseenter', this.callbacks.onMouseEnter)
        this.DOM.scope.removeEventListener('mousemove', this.callbacks.onMouseMove)
    },

    bind(){
        this.callbacks.onMouseEnter = this.onMouseEnter.bind(this)
        this.callbacks.onMouseMove = e => {
        if( this.mouseMoveRAF ) 
            cancelAnimationFrame(this.mouseMoveRAF)

        this.mouseMoveRAF = requestAnimationFrame(this.onMouseMove.bind(this, e))
        }
        
        this.DOM.scope.addEventListener('mouseenter', this.callbacks.onMouseEnter)
        this.DOM.scope.addEventListener('mousemove', this.callbacks.onMouseMove)
    },
    
    // calculate the thumbs container width
    onMouseEnter(e){
        this.nextMore = this.prevMore = false // reset

        this.containerWidth       = this.DOM.wrap.clientWidth;
        this.scrollWidth          = this.DOM.wrap.scrollWidth; 
        // padding in percentage of the area which the mouse movement affects
        this.padding              = 0.2 * this.containerWidth; 
        this.posFromLeft          = this.DOM.wrap.getBoundingClientRect().left;
        var stripePos             = e.pageX - this.padding - this.posFromLeft;
        this.pos                  = stripePos / (this.containerWidth - this.padding*2);
        this.scrollPos            = (this.scrollWidth - this.containerWidth ) * this.pos;

        // temporary add smoothness to the scroll 
        this.DOM.wrap.style.scrollBehavior = 'smooth';
        
        if( this.scrollPos < 0 )
        this.scrollPos = 0;
        
        if( this.scrollPos > (this.scrollWidth - this.containerWidth) )
        this.scrollPos = this.scrollWidth - this.containerWidth

        this.DOM.wrap.scrollLeft = this.scrollPos
        this.DOM.scope.style.setProperty('--scrollWidth',  (this.containerWidth / this.scrollWidth) * 100 + '%');
        this.DOM.scope.style.setProperty('--scrollLleft',  (this.scrollPos / this.scrollWidth ) * 100 + '%');

        // lock UI until mouse-enter scroll is finihsed, after aprox 200ms
        clearTimeout(this.animated)
        this.animated = setTimeout(() => {
        this.animated = null
        this.DOM.wrap.style.scrollBehavior = 'auto';
        }, 200)

        return this
    },

    // move the stripe left or right according to mouse position
    onMouseMove(e){
        // don't move anything until inital movement on 'mouseenter' has finished
        if( this.animated ) return

        this.ratio = this.scrollWidth / this.containerWidth
        
        // the mouse X position, "normalized" to the carousel position
        var stripePos = e.pageX - this.padding - this.posFromLeft 
        
        if( stripePos < 0 )
            stripePos = 0

        // calculated position between 0 to 1
        this.pos = stripePos / (this.containerWidth - this.padding*2) 
        
        // calculate the percentage of the mouse position within the carousel
        this.scrollPos = (this.scrollWidth - this.containerWidth ) * this.pos 

        this.DOM.wrap.scrollLeft = this.scrollPos
        
        // update scrollbar
        if( this.scrollPos < (this.scrollWidth - this.containerWidth) )
        this.DOM.scope.style.setProperty('--scrollLleft',  (this.scrollPos / this.scrollWidth ) * 100 + '%');

        // check if element has reached an edge
        this.prevMore = this.DOM.wrap.scrollLeft > 0
        this.nextMore = this.scrollWidth - this.containerWidth - this.DOM.wrap.scrollLeft > 5
        
        this.DOM.scope.setAttribute('data-at',
        (this.prevMore  ? 'left ' : ' ')
        + (this.nextMore ? 'right' : '')
        )
    }
    }
            
    var carouselElm = document.querySelector('.carousel')
    new HoverCarousel(carouselElm)                          
    </script>
<body>
    </body>
 <?php 
   var images = [
    'http://ppcdn.500px.org/23718959/f216a69d1cb7667436a1e6a33c7e4b2894d0fe18/4.jpg',
    'http://ppcdn.500px.org/72233281/5e145f8f95427805045dbaf1ec2cf17bcb4b25b3/3.jpg',
    'http://ppcdn.500px.org/72190239/1ac03d25646f92afd4d80ed8e6f2c1cfc78635b5/4.jpg',
    'http://ppcdn.500px.org/58739696/b0c33312552e8581ef457720bb924f16421dcec8/3.jpg',
    'http://ppcdn.500px.org/43028892/84b059f25810f07962ae46e63c44c625b2f9b9e6/2048.jpg',
    'http://ppcdn.500px.org/33154839/a53aeaca99ee94a9803f445fd30163142f308829/3.jpg',
    'http://ppcdn.500px.org/17981845/d1fdacaf5818108c248b05cd472329d314e732d3/3.jpg'
  ]

.carousel
  .wrap
    ul
      each image in images
        li 
          img(src=image)
 ?>
</html>