<x-layouts.app title="Forge | infinitesugar">
    <style>
        .forge-page {
            width: 100vw;
            margin-inline: calc(50% - 50vw);
            background: #e9e4d8;
        }

        .forge-page-wrap {
            width: min(72vw, 1390px);
            margin-inline: auto;
            padding-block: clamp(3.8rem, 6vw, 5rem) clamp(4rem, 6vw, 5.6rem);
        }

        .forge-page-title {
            margin: 0 0 1.65rem;
            color: #a8873f;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(4.2rem, 6vw, 6rem);
            font-weight: 600;
            line-height: .95;
        }

        .forge-page-line,
        .forge-page-signals p,
        .forge-page-built,
        .forge-page-action h2,
        .forge-page-bottom p {
            margin: 0;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2.25rem, 3.1vw, 3rem);
            font-weight: 600;
            line-height: 1.03;
        }

        .forge-page-line + .forge-page-line {
            margin-top: .6rem;
        }

        .forge-page-gold {
            color: #a8873f;
        }

        .forge-page-signals {
            margin-top: 1.55rem;
        }

        .forge-page-built {
            max-width: 760px;
            margin-top: 1.65rem;
        }

        .forge-page-action {
            margin-top: 1.8rem;
        }

        .forge-page-action h2 {
            margin-bottom: .25rem;
        }

        .forge-page-action ul {
            margin: 0;
            padding-left: 1.35rem;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
        }

        .forge-page-action li {
            max-width: 650px;
            font-size: clamp(1.55rem, 2vw, 1.9rem);
            font-weight: 500;
            line-height: .94;
        }

        .forge-page-bottom {
            margin-top: 1.65rem;
        }

        @media (max-width: 991.98px) {
            .forge-page-wrap {
                width: min(86vw, 720px);
            }
        }

        @media (max-width: 575.98px) {
            .forge-page-wrap {
                width: 100%;
                padding: 3rem 1.25rem 4rem;
            }

            .forge-page-title {
                font-size: 4rem;
            }

            .forge-page-line,
            .forge-page-signals p,
            .forge-page-built,
            .forge-page-action h2,
            .forge-page-bottom p {
                font-size: 2.15rem;
            }

            .forge-page-action li {
                font-size: 1.35rem;
                line-height: 1.02;
            }
        }
    </style>

    <section class="forge-page">
        <div class="forge-page-wrap">
            <h1 class="forge-page-title">FORGE</h1>

            <p class="forge-page-line"><span>Turns </span><span class="forge-page-gold">live behavioral signals into measurable deal movement.</span></p>
            <p class="forge-page-line">Makes timing visible — where<br>revenue is protected or lost.</p>

            <div class="forge-page-signals">
                <p class="forge-page-gold">Composure shifting.</p>
                <p class="forge-page-gold">Authority strengthening.</p>
                <p class="forge-page-gold">Probability changing.</p>
                <p class="forge-page-gold">During it.</p>
            </div>

            <p class="forge-page-built">Built for operators responsible for <span class="forge-page-gold">outcomes.</span></p>

            <div class="forge-page-action">
                <h2>In Action</h2>
                <ul>
                    <li>Protect pricing power under pressure</li>
                    <li>Prevent authority erosion</li>
                    <li>Time the close when control is strongest</li>
                    <li>Improve forecast accuracy through behavioral consistency</li>
                </ul>
            </div>

            <div class="forge-page-bottom">
                <p>Signals surface live.</p>
                <p>Results become measurable.</p>
            </div>
        </div>
    </section>
</x-layouts.app>
