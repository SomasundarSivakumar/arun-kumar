// ESM imports removed: loaded via CDN globally in index.php

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// ─────────────────────────────────────────────────────────────────────────────
// 0. Initialize Premium Preloader / Loading Screen
// ─────────────────────────────────────────────────────────────────────────────
function initPreloader() {
  const preloader = document.getElementById('preloader');
  if (!preloader) return;

  const startTime = Date.now();
  const MIN_LOADING_TIME = 1500; // Minimum duration in milliseconds
  let faded = false;

  const fadeOut = () => {
    if (faded) return;
    faded = true;

    const elapsed = Date.now() - startTime;
    const remainingTime = Math.max(0, MIN_LOADING_TIME - elapsed);

    setTimeout(() => {
      preloader.classList.add('loaded');
      setTimeout(() => {
        preloader.remove();
      }, 750);
    }, remainingTime);
  };

  if (document.readyState === 'complete') {
    fadeOut();
  } else {
    window.addEventListener('load', fadeOut);
  }

  // Fallback: force dismiss the preloader after 4 seconds if load event hasn't fired
  setTimeout(fadeOut, 4000);
}
initPreloader();

// ─────────────────────────────────────────────────────────────────────────────
// 1. Initialize Lenis Smooth Scroll
// ─────────────────────────────────────────────────────────────────────────────
const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  orientation: 'vertical',
  gestureOrientation: 'vertical',
  smoothWheel: true,
  anchors: true,
});

lenis.on('scroll', ScrollTrigger.update);

gsap.ticker.add((time) => {
  lenis.raf(time * 1000);
});

gsap.ticker.lagSmoothing(0);
window.lenis = lenis;

// Sync ScrollTrigger with Lenis virtual scroll position
ScrollTrigger.scrollerProxy(document.documentElement, {
  scrollTop(value) {
    if (arguments.length) {
      lenis.scrollTo(value, { immediate: true });
    }
    return lenis.scroll;
  },
  getBoundingClientRect() {
    return {
      top: 0,
      left: 0,
      width: window.innerWidth,
      height: window.innerHeight,
    };
  },
  pinType: document.documentElement.style.transform ? 'transform' : 'fixed',
});

ScrollTrigger.addEventListener('refresh', () => lenis.resize());

// ─────────────────────────────────────────────────────────────────────────────
// UTILITY: Generic section text reveal helper
// Animates an array of elements with a fade-up reveal tied to a trigger.
// ─────────────────────────────────────────────────────────────────────────────
function revealSection(trigger, targets, options = {}) {
  const {
    y = 50,
    x = 0,
    opacity = 0,
    duration = 0.9,
    stagger = 0.13,
    ease = 'power3.out',
    start = 'top 82%',
    toggleActions = 'play reverse play reverse',
    delay = 0,
  } = options;

  if (!targets || (targets.length !== undefined && targets.length === 0)) return;

  gsap.from(targets, {
    y,
    x,
    opacity,
    duration,
    stagger,
    ease,
    delay,
    scrollTrigger: {
      trigger,
      start,
      toggleActions,
    },
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// UTILITY: Hero typing effect
// ─────────────────────────────────────────────────────────────────────────────
// Dynamic hero title — set by index.php from the database
const HERO_ROLE_TEXT = (typeof window.HERO_TITLE === 'string' && window.HERO_TITLE.trim())
  ? window.HERO_TITLE
  : 'Fractional Chief Data Officer';

function typeText(element, text, speed) {
  if (element.typingTimeout) clearTimeout(element.typingTimeout);
  element.textContent = '';
  let i = 0;
  function type() {
    if (i < text.length) {
      element.textContent += text.charAt(i);
      i++;
      element.typingTimeout = setTimeout(type, speed);
    }
  }
  type();
}

// ─────────────────────────────────────────────────────────────────────────────
// 2. HERO BANNER — entrance timeline (plays on load, replays when returning)
// ─────────────────────────────────────────────────────────────────────────────
let heroTimeline = null;

function initHeroScrollAnimations() {
  const heroSection = document.getElementById('hero');
  if (!heroSection) return;

  const heroName = heroSection.querySelector('.hero-name');
  const heroRole = document.getElementById('hero-role');
  const taglines = Array.from(heroSection.querySelectorAll('.hero-tagline > span'));
  const heroEls = [heroName, heroRole, ...taglines].filter(Boolean);
  if (heroEls.length === 0) return;

  const resetHero = () => {
    if (heroTimeline) heroTimeline.kill();
    if (heroRole?.typingTimeout) clearTimeout(heroRole.typingTimeout);
    gsap.set(heroEls, { opacity: 0, y: 40 });
    if (heroRole) heroRole.textContent = '';
  };

  const playHero = () => {
    resetHero();

    heroTimeline = gsap.timeline({ defaults: { ease: 'power3.out' } });

    if (heroName) {
      heroTimeline.to(heroName, { y: 0, opacity: 1, duration: 0.95 });
    }
    if (heroRole) {
      heroTimeline.to(heroRole, { y: 0, opacity: 1, duration: 0.8 }, '-=0.5');
      heroTimeline.call(() => typeText(heroRole, HERO_ROLE_TEXT, 60));
    }
    if (taglines.length > 0) {
      heroTimeline.to(
        taglines,
        { y: 0, opacity: 1, stagger: 0.1, duration: 0.8 },
        heroRole ? '-=0.5' : '-=0.4',
      );
    }
  };

  playHero();

  ScrollTrigger.create({
    trigger: heroSection,
    start: 'top 75%',
    end: 'bottom top',
    onEnterBack: playHero,
  });

  window.playHero = playHero;
}

// ─────────────────────────────────────────────────────────────────────────────
// 3. Responsive Navigation & Smooth Scrolling (Lenis + GSAP integration)
// ─────────────────────────────────────────────────────────────────────────────

// Setup mobile menu drawer toggles and state
function initMobileNavigation() {
  const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const hamburgerPath = document.getElementById('hamburger-path');

  if (!mobileMenuToggle || !mobileMenu) return;

  let isMenuOpen = false;

  const openMobileMenu = () => {
    isMenuOpen = true;
    mobileMenu.classList.add('open');
    if (hamburgerPath) {
      hamburgerPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
    }
    lenis.stop();
  };

  const closeMobileMenu = () => {
    isMenuOpen = false;
    mobileMenu.classList.remove('open');
    if (hamburgerPath) {
      hamburgerPath.setAttribute('d', 'M4 6h16M4 12h16M4 18h16');
    }
    lenis.start();
  };

  mobileMenuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    if (isMenuOpen) {
      closeMobileMenu();
    } else {
      openMobileMenu();
    }
  });

  // Register on window object for accessibility across components
  window.closeMobileMenu = closeMobileMenu;
}

// Set up smooth scrolling for all anchor links
function initSmoothScrollLinks() {
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href');
      if (!targetId || targetId === '#') return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        event.preventDefault();
        event.stopPropagation();

        // Close mobile menu if open
        if (typeof window.closeMobileMenu === 'function') {
          window.closeMobileMenu();
        }

        // Calculate responsive offset for mobile top bar (80px)
        const isMobile = window.innerWidth < 768;
        const scrollOffset = isMobile ? -80 : 0;

        lenis.scrollTo(targetId, {
          duration: 1.2,
          offset: scrollOffset,
          onComplete: () => {
            if (targetId === '#hero' && typeof window.playHero === 'function') {
              window.playHero();
            }
          },
        });
      }
    });
  });
}

// Set up active section highlighting spy
function initActiveSectionSpy() {
  const navItems = document.querySelectorAll('#main-sidebar .nav-item');
  const mobileNavItems = document.querySelectorAll('#mobile-menu .mobile-nav-item');
  const sections = [];

  // Map links to corresponding DOM sections
  navItems.forEach((item) => {
    const href = item.getAttribute('href');
    if (href && href.startsWith('#')) {
      const sec = document.querySelector(href);
      if (sec) {
        sections.push({ href, element: sec });
      }
    }
  });

  if (sections.length === 0) return;

  sections.forEach((sec) => {
    ScrollTrigger.create({
      trigger: sec.element,
      start: 'top 45%', // section enters viewport active zone
      end: 'bottom 45%', // section leaves active zone
      onToggle: (self) => {
        if (self.isActive) {
          // Sync desktop sidebar
          navItems.forEach((item) => {
            if (item.getAttribute('href') === sec.href) {
              item.classList.add('active');
            } else {
              item.classList.remove('active');
            }
          });
          // Sync mobile menu drawer
          mobileNavItems.forEach((item) => {
            if (item.getAttribute('href') === sec.href) {
              item.classList.add('active');
            } else {
              item.classList.remove('active');
            }
          });
        }
      },
    });
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// 4. Capabilities Timeline ScrollTrigger Animation
// ─────────────────────────────────────────────────────────────────────────────
// 5. Capabilities Timeline ScrollTrigger Animation (Moved to initCapabilitiesSectionAnimations)
// ─────────────────────────────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────────────────────────────
// 6. Services Cards ScrollTrigger Entrance
// ─────────────────────────────────────────────────────────────────────────────
function initServicesAnimations() {
  const servicesSection = document.getElementById('services');
  if (!servicesSection) return;

  const header = servicesSection.querySelector('h2')?.parentElement;
  if (header && header.children.length > 0) {
    gsap.from(Array.from(header.children), {
      y: 35,
      opacity: 0,
      duration: 0.85,
      stagger: 0.12,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: servicesSection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  const cards = servicesSection.querySelectorAll('.service-card');
  if (cards.length > 0) {
    cards.forEach((card) => {
      // First, animate the card container itself
      gsap.from(card, {
        opacity: 0,
        y: 50,
        duration: 0.8,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: card,
          start: 'top 85%',
          toggleActions: 'play reverse play reverse',
        },
      });

      // Then stagger the contents inside each card
      const cardContents = card.querySelectorAll('div, h3, p, li');
      if (cardContents.length > 0) {
        gsap.from(cardContents, {
          opacity: 0,
          y: 20,
          duration: 0.65,
          stagger: 0.05,
          ease: 'power2.out',
          immediateRender: false,
          scrollTrigger: {
            trigger: card,
            start: 'top 80%',
            toggleActions: 'play reverse play reverse',
          },
        });
      }
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 7. Ideal Clients Showcase Interactive Handler
// ─────────────────────────────────────────────────────────────────────────────
function initIdealClientsAnimations() {
  const section = document.getElementById('ideal-clients');
  if (!section) return;

  // Section Header
  const header = section.querySelector('h2')?.parentElement;
  if (header) {
    const headings = header.querySelectorAll('h2, h3');
    if (headings.length > 0) {
      gsap.from(headings, {
        y: 40,
        opacity: 0,
        duration: 0.85,
        stagger: 0.15,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: section,
          start: 'top 85%',
          toggleActions: 'play reverse play reverse',
        },
      });
    }
  }

  const clientNavItems  = section.querySelectorAll('.client-nav-item');
  const defaultPanel    = document.getElementById('panel-default');
  const showcasePanels  = document.querySelectorAll('.showcase-panel');

  if (clientNavItems.length > 0) {
    const activeItem = document.querySelector('.client-nav-item.active-item');
    let initialPanel = defaultPanel;

    if (activeItem) {
      const targetId    = activeItem.getAttribute('data-target');
      const targetPanel = document.getElementById(`panel-${targetId}`);
      if (targetPanel) initialPanel = targetPanel;
    }

    if (initialPanel) {
      initialPanel.classList.add('active-panel');
      gsap.set(initialPanel, { opacity: 1, y: 0, display: 'flex' });
      const paths = initialPanel.querySelectorAll('.draw-path');
      if (paths.length > 0) gsap.set(paths, { strokeDashoffset: 0 });
    }

    showcasePanels.forEach((panel) => {
      if (panel !== initialPanel) {
        gsap.set(panel, { opacity: 0, y: 15, display: 'none', pointerEvents: 'none' });
      }
    });

    clientNavItems.forEach((item) => {
      item.addEventListener('mouseenter', () => {
        const targetId    = item.getAttribute('data-target');
        const targetPanel = document.getElementById(`panel-${targetId}`);

        if (targetPanel && !targetPanel.classList.contains('active-panel')) {
          clientNavItems.forEach((nav) => nav.classList.remove('active-item'));
          item.classList.add('active-item');

          showcasePanels.forEach((panel) => {
            if (panel.classList.contains('active-panel')) {
              panel.classList.remove('active-panel');
              gsap.to(panel, {
                opacity: 0,
                y: 15,
                duration: 0.4,
                ease: 'power2.inOut',
                onComplete: () => gsap.set(panel, { pointerEvents: 'none', display: 'none' }),
              });
            }
          });

          targetPanel.classList.add('active-panel');
          gsap.set(targetPanel, { pointerEvents: 'auto', display: 'flex' });
          gsap.fromTo(targetPanel,
            { opacity: 0, y: 15 },
            { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }
          );

          const paths = targetPanel.querySelectorAll('.draw-path');
          if (paths.length > 0) {
            gsap.fromTo(paths,
              { strokeDashoffset: 150 },
              { strokeDashoffset: 0, duration: 1.2, ease: 'power1.inOut', stagger: 0.1 }
            );
          }
        }
      });
    });

    // Section entrance stagger
    gsap.from(clientNavItems, {
      opacity: 0,
      x: -35,
      duration: 0.85,
      stagger: 0.1,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: '#ideal-clients',
        start: 'top 80%',
        toggleActions: 'play reverse play reverse',
      },
    });

    const visualPanel = section.querySelector('.glassmorphism');
    if (visualPanel) {
      gsap.from(visualPanel, {
        opacity: 0,
        x: 40,
        duration: 0.9,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: '#ideal-clients',
          start: 'top 80%',
          toggleActions: 'play reverse play reverse',
        },
      });
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 8. Swiper 3D Coverflow Carousel Initialization
// ─────────────────────────────────────────────────────────────────────────────
function initImpactSwiper() {
  if (window.impactSwiper) return;

  if (typeof Swiper !== 'undefined') {
    const slideCount = document.querySelectorAll('.impact-swiper .swiper-slide').length;
    const shouldLoop = slideCount > 2;

    const swiper = new Swiper('.impact-swiper', {
      effect: 'coverflow',
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: 'auto',
      loop: shouldLoop,
      speed: 800,
      slideToClickedSlide: true,
      touchRatio: 1.25,
      resistanceRatio: 0.65,
      observer: true,
      observeParents: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
      coverflowEffect: {
        rotate: 12,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: false,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      breakpoints: {
        320: { spaceBetween: 20 },
        768: { spaceBetween: 30 },
        1024: { spaceBetween: 40 },
      },
    });

    if (swiper.autoplay) swiper.autoplay.start();
    setTimeout(() => swiper.update(), 150);
    window.impactSwiper = swiper;
  } else {
    console.error('Swiper is not defined. Make sure CDN scripts are loaded correctly.');
  }
}

if (document.readyState === 'complete') {
  initImpactSwiper();
} else {
  window.addEventListener('load', initImpactSwiper);
}

// ─────────────────────────────────────────────────────────────────────────────
// 9. Experience Timeline — GSAP ScrollTrigger Animations
// ─────────────────────────────────────────────────────────────────────────────
function initExperienceAnimations() {
  const expSection = document.getElementById('experience');
  if (!expSection) return;

  const expHeader   = expSection.querySelector('h2')?.parentElement;
  const expItems    = expSection.querySelectorAll('.exp-item');
  const lineFill    = expSection.querySelector('.exp-line-fill');

  if (expHeader && expHeader.children.length > 0) {
    gsap.from(Array.from(expHeader.children), {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: expHeader,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  if (lineFill) {
    gsap.to(lineFill, {
      height: '100%',
      ease: 'none',
      scrollTrigger: {
        trigger: '.exp-timeline',
        start: 'top 70%',
        end: 'bottom 30%',
        scrub: 0.8,
      },
    });
  }

  expItems.forEach((item, index) => {
    const card   = item.querySelector('.exp-card');
    const isLeft = index % 2 !== 0;

    if (card) {
      // Card container reveal
      gsap.from(card, {
        x: isLeft ? -65 : 65,
        opacity: 0,
        duration: 0.85,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: item,
          start: 'top 80%',
          toggleActions: 'play reverse play reverse',
        },
      });

      // Inner card text elements stagger
      const elements = card.querySelectorAll('.flex.items-center, h4, p');
      if (elements.length > 0) {
        gsap.from(elements, {
          opacity: 0,
          y: 15,
          duration: 0.65,
          stagger: 0.08,
          ease: 'power2.out',
          immediateRender: false,
          scrollTrigger: {
            trigger: item,
            start: 'top 78%',
            toggleActions: 'play reverse play reverse',
          },
        });
      }
    }

    ScrollTrigger.create({
      trigger: item,
      start: 'top 65%',
      end: 'bottom 35%',
      onEnter:     () => item.classList.add('is-active'),
      onLeave:     () => item.classList.remove('is-active'),
      onEnterBack: () => item.classList.add('is-active'),
      onLeaveBack: () => item.classList.remove('is-active'),
    });
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// 10. The Difference / Why Work With Me — ScrollTrigger Animations
// ─────────────────────────────────────────────────────────────────────────────
function initDifferenceAnimations() {
  const differenceSection = document.getElementById('the-difference');
  if (!differenceSection) return;

  const header = differenceSection.querySelector('h2')?.parentElement;
  const items  = differenceSection.querySelectorAll('.difference-item');

  if (header && header.children.length > 0) {
    gsap.from(Array.from(header.children), {
      y: 40,
      opacity: 0,
      duration: 0.85,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: differenceSection,
        start: 'top 80%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  if (items.length > 0) {
    gsap.from(items, {
      y: 45,
      opacity: 0,
      duration: 0.85,
      stagger: 0.1,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: differenceSection,
        start: 'top 75%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 11. Call to Action Section ScrollTrigger Animations
// ─────────────────────────────────────────────────────────────────────────────
function initCTAAnimations() {
  const ctaSection = document.getElementById('cta');
  if (!ctaSection) return;

  const container = ctaSection.querySelector('.cta-container');
  const links     = ctaSection.querySelectorAll('.cta-link-item');

  if (container && container.children.length > 0) {
    gsap.from(Array.from(container.children), {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: ctaSection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  if (links.length > 0) {
    gsap.from(links, {
      y: 55,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: ctaSection,
        start: 'top 75%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 12. Expertise (Marquee) Section — Header Text Reveal
// ─────────────────────────────────────────────────────────────────────────────
function initExpertiseSectionAnimations() {
  const expertiseSection = document.getElementById('expertise');
  if (!expertiseSection) return;

  const header = expertiseSection.querySelector('h2')?.parentElement;
  if (header) {
    const headingEls = header.querySelectorAll('h2, p');
    if (headingEls.length > 0) {
      gsap.from(headingEls, {
        y: 40,
        opacity: 0,
        duration: 0.85,
        stagger: 0.15,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: expertiseSection,
          start: 'top 85%',
          toggleActions: 'play reverse play reverse',
        },
      });
    }
  }

  const track = expertiseSection.querySelector('.marquee-track');
  if (track) {
    gsap.from(track, {
      y: 45,
      opacity: 0,
      duration: 1.0,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: expertiseSection,
        start: 'top 80%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 13. Opportunity Section — Text & Card Reveal
// ─────────────────────────────────────────────────────────────────────────────
function initOpportunitySectionAnimations() {
  const opportunitySection = document.getElementById('opportunity');
  if (!opportunitySection) return;

  // Section header (including sub-text description)
  const header = opportunitySection.querySelector('h2')?.parentElement;
  const headings = header ? Array.from(header.children) : [];
  if (headings.length > 0) {
    gsap.from(headings, {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: opportunitySection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  // The two side-by-side cards (friction + CDO)
  const cards = opportunitySection.querySelectorAll('.flex-wrap.lg\\:flex-nowrap > div');
  cards.forEach((card) => {
    // Animate card container itself
    gsap.from(card, {
      opacity: 0,
      y: 45,
      duration: 0.85,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: card,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });

    // Stagger text and sub-text elements inside the cards
    const innerElements = card.querySelectorAll('.flex.items-center, h3, p, .pl-4, span.px-3\\.5');
    if (innerElements.length > 0) {
      gsap.from(innerElements, {
        opacity: 0,
        y: 20,
        duration: 0.7,
        stagger: 0.08,
        ease: 'power2.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: card,
          start: 'top 80%',
          toggleActions: 'play reverse play reverse',
        },
      });
    }
  });

  // Bottom quote / sign-off text
  const quote = opportunitySection.querySelector('.max-w-4xl');
  if (quote && quote.children.length > 0) {
    gsap.from(Array.from(quote.children), {
      y: 35,
      opacity: 0,
      duration: 0.85,
      stagger: 0.12,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: quote,
        start: 'top 88%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 14. About Section — Text & Image Reveal
// ─────────────────────────────────────────────────────────────────────────────
function initAboutSectionAnimations() {
  const aboutSections = document.querySelectorAll('section');
  let aboutSection = null;
  aboutSections.forEach((sec) => {
    const h2 = sec.querySelector('h2');
    if (h2 && h2.textContent.trim().toUpperCase() === 'ABOUT') {
      aboutSection = sec;
    }
  });

  if (!aboutSection) return;

  // Section heading
  const header = aboutSection.querySelector('h2')?.parentElement;
  const headingEls = header ? Array.from(header.children) : [];
  if (headingEls.length > 0) {
    gsap.from(headingEls, {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.12,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: aboutSection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  // Left text column paragraphs
  const textCols = Array.from(aboutSection.querySelectorAll('.flex-col.gap-6 > p, .flex-col.gap-6 > div'));
  if (textCols.length > 0) {
    gsap.from(textCols, {
      y: 35,
      opacity: 0,
      duration: 0.85,
      stagger: 0.12,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: aboutSection,
        start: 'top 78%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  // Right image column
  const imageCol = aboutSection.querySelector('.relative.min-h-\\[400px\\]');
  if (imageCol) {
    gsap.from(imageCol, {
      x: 60,
      opacity: 0,
      duration: 1.0,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: aboutSection,
        start: 'top 80%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 15. Capabilities Section — Header Reveal
// ─────────────────────────────────────────────────────────────────────────────
function initCapabilitiesSectionAnimations() {
  const capSection = document.getElementById('capabilities');
  if (!capSection) return;

  const header = capSection.querySelector('h2')?.parentElement;
  const headings = header ? Array.from(header.children) : [];
  if (headings.length > 0) {
    gsap.from(headings, {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: capSection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  const timelineContent = capSection.querySelector('.flex.flex-row.gap-8');
  const visualContainer = document.getElementById('timeline-visual-container');

  if (timelineContent) {
    gsap.from(timelineContent, {
      y: 40,
      opacity: 0,
      duration: 0.9,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: '#timeline-track-container',
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  if (visualContainer) {
    gsap.from(visualContainer, {
      x: 40,
      opacity: 0,
      duration: 1.0,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: '#timeline-track-container',
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  // Vertical timeline line height and active state mapping
  const timelineScrollLine   = document.getElementById('timeline-scroll-line');
  const timelineDots         = document.querySelectorAll('.timeline-dot');
  const timelineItems        = document.querySelectorAll('.timeline-item');
  const timelineVisuals      = document.querySelectorAll('.timeline-visual');
  const timelineHeight       = 600;

  if (timelineScrollLine) {
    gsap.to(timelineScrollLine, {
      height: timelineHeight,
      ease: 'none',
      scrollTrigger: {
        trigger: '#timeline-track-container',
        start: 'top 45%',
        end: 'bottom 55%',
        scrub: 0.5,
        onUpdate: (self) => {
          const progress    = self.progress;
          const currentHeight = progress * timelineHeight;
          let activeIndex   = 0;

          timelineDots.forEach((dot, index) => {
            const dotTop = (index / (timelineDots.length - 1)) * timelineHeight;
            if (currentHeight >= dotTop - 15) {
              activeIndex = index;
              dot.classList.remove('bg-[#121216]', 'border-white/10');
              dot.classList.add('bg-[#60a5fa]', 'border-[#60a5fa]', 'shadow-[0_0_12px_rgba(96,165,250,0.65)]');
              const innerDot = dot.querySelector('.inner-dot');
              if (innerDot) {
                innerDot.classList.remove('bg-white/20');
                innerDot.classList.add('bg-black', 'scale-125');
              }
              if (timelineItems[index]) {
                timelineItems[index].classList.remove('opacity-25', 'translate-x-4');
                timelineItems[index].classList.add('opacity-100', 'translate-x-0');
              }
            } else {
              dot.classList.remove('bg-[#60a5fa]', 'border-[#60a5fa]', 'shadow-[0_0_12px_rgba(96,165,250,0.65)]');
              dot.classList.add('bg-[#121216]', 'border-white/10');
              const innerDot = dot.querySelector('.inner-dot');
              if (innerDot) {
                innerDot.classList.remove('bg-black', 'scale-125');
                innerDot.classList.add('bg-white/20');
              }
              if (timelineItems[index]) {
                timelineItems[index].classList.add('opacity-25', 'translate-x-4');
                timelineItems[index].classList.remove('opacity-100', 'translate-x-0');
              }
            }
          });

          timelineVisuals.forEach((visual, index) => {
            if (index === activeIndex) {
              visual.classList.add('active');
            } else {
              visual.classList.remove('active');
            }
          });
        },
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 16. Impact Section — Header + Swiper Fade In
// ─────────────────────────────────────────────────────────────────────────────
function initImpactSectionAnimations() {
  const impactSection = document.getElementById('impact');
  if (!impactSection) return;

  // Main heading and description elements
  const header = impactSection.querySelector('h2')?.parentElement;
  const headings = header ? Array.from(header.children) : [];
  if (headings.length > 0) {
    gsap.from(headings, {
      y: 45,
      opacity: 0,
      duration: 0.9,
      stagger: 0.15,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: impactSection,
        start: 'top 85%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }

  const swiper = impactSection.querySelector('.impact-swiper');
  if (swiper) {
    gsap.from(swiper, {
      y: 55,
      opacity: 0,
      duration: 1.0,
      ease: 'power3.out',
      immediateRender: false,
      scrollTrigger: {
        trigger: impactSection,
        start: 'top 80%',
        toggleActions: 'play reverse play reverse',
      },
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// 17. Ideal Clients Section — Header Reveal (Merged with Ideal Clients Init)
// ─────────────────────────────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────────────────────────────
// 18. Technology Expertise Section — Cards + Header Reveal
// ─────────────────────────────────────────────────────────────────────────────
function initTechnologyExpertiseSectionAnimations() {
  const techSection = document.getElementById('technology');
  if (!techSection) return;

  const header = techSection.querySelector('h2')?.parentElement;
  const headings = header ? Array.from(header.children) : [];
  if (headings.length > 0) {
    gsap.fromTo(headings,
      { y: 45, opacity: 0 },
      {
        y: 0,
        opacity: 1,
        duration: 0.9,
        stagger: 0.15,
        ease: 'power3.out',
        immediateRender: false,
        scrollTrigger: {
          trigger: techSection,
          start: 'top 85%',
          toggleActions: 'play reverse play reverse',
        },
      }
    );
  }

  // Animate category headers
  const categories = techSection.querySelectorAll('.flex.flex-col > .text-2xl');
  if (categories.length > 0) {
    categories.forEach((cat) => {
      gsap.fromTo(cat,
        { y: 30, opacity: 0 },
        {
          y: 0,
          opacity: 1,
          duration: 0.8,
          ease: 'power3.out',
          immediateRender: false,
          scrollTrigger: {
            trigger: cat,
            start: 'top 88%',
            toggleActions: 'play reverse play reverse',
          },
        }
      );
    });
  }

  // Premium cards stagger reveal (row-by-row)
  const grids = techSection.querySelectorAll('.grid');
  grids.forEach((grid) => {
    const cards = grid.querySelectorAll('.premium-card');
    if (cards.length > 0) {
      gsap.fromTo(cards,
        { y: 40, opacity: 0, scale: 0.96 },
        {
          y: 0,
          opacity: 1,
          scale: 1,
          duration: 0.8,
          stagger: 0.08,
          ease: 'power3.out',
          immediateRender: false,
          scrollTrigger: {
            trigger: grid,
            start: 'top 88%',
            toggleActions: 'play reverse play reverse',
          },
        }
      );
    }
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// Bootstrap — Initialize after DOM is ready
// ─────────────────────────────────────────────────────────────────────────────
function initAll() {
  initHeroScrollAnimations();
  initMobileNavigation();
  initSmoothScrollLinks();
  initActiveSectionSpy();
  
  initExpertiseSectionAnimations();
  initOpportunitySectionAnimations();
  initAboutSectionAnimations();
  initCapabilitiesSectionAnimations();
  initServicesAnimations();
  initIdealClientsAnimations();
  initImpactSectionAnimations();
  initTechnologyExpertiseSectionAnimations();
  initExperienceAnimations();
  initDifferenceAnimations();
  initCTAAnimations();

  ScrollTrigger.refresh();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
