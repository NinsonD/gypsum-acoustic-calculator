<?php

declare(strict_types=1);

final class ContentRepository
{
    public function brands(): array
    {
        return [
            ['id' => 'knauf', 'name' => 'Knauf', 'slug' => 'knauf', 'description' => 'Gypsum board, framing, jointing, fire-rated, and acoustic drywall systems.'],
            ['id' => 'gyproc', 'name' => 'Gyproc', 'slug' => 'gyproc', 'description' => 'Plasterboard systems, shaft walls, ceiling linings, and tested partition assemblies.'],
            ['id' => 'usg-boral', 'name' => 'USG Boral', 'slug' => 'usg-boral', 'description' => 'Ceiling tiles, drywall boards, metal framing, and interior finishing systems.'],
            ['id' => 'rockfon', 'name' => 'Rockfon', 'slug' => 'rockfon', 'description' => 'Stone wool acoustic ceiling systems with high NRC and fire performance.'],
            ['id' => 'knauf-danoline', 'name' => 'Knauf Danoline', 'slug' => 'knauf-danoline', 'description' => 'Perforated gypsum acoustic panels for ceilings, walls, auditoriums, and offices.'],
            ['id' => 'gypsemna', 'name' => 'Gypsemna', 'slug' => 'gypsemna', 'description' => 'Locally manufactured gypsum boards for regional ceiling and partition projects.'],
        ];
    }

    public function products(): array
    {
        return [
            [
                'name' => 'Standard Gypsum Ceiling System',
                'slug' => 'standard-gypsum-ceiling-system',
                'brand_id' => 'knauf',
                'category' => 'Gypsum ceiling',
                'summary' => 'Suspended gypsum board ceiling with main channel, furring channel, hangers, and jointing.',
                'description' => 'General interior gypsum ceiling assembly for offices, villas, retail areas, and corridors.',
                'nrc' => 'N/A',
                'stc' => 'N/A',
                'fire_rating' => 'Assembly dependent',
                'specs' => ['Board size' => '1200 x 2400 mm typical', 'Furring spacing' => '400 mm typical', 'Main channel spacing' => '1200 mm typical'],
            ],
            [
                'name' => 'Perforated Acoustic Gypsum Ceiling',
                'slug' => 'perforated-acoustic-gypsum-ceiling',
                'brand_id' => 'knauf-danoline',
                'category' => 'Acoustic ceiling',
                'summary' => 'Perforated gypsum panels for reverberation control and architectural ceilings.',
                'description' => 'Acoustic gypsum system using perforated panels, acoustic fleece, and cavity absorption.',
                'nrc' => 'Typical 0.55 - 0.85',
                'stc' => 'N/A',
                'fire_rating' => 'Verify tested assembly',
                'specs' => ['Backing' => 'Acoustic fleece with optional mineral wool', 'Finish' => 'Factory finish or site paint', 'Use' => 'Auditoriums, meeting rooms, schools, mosques'],
            ],
            [
                'name' => 'Mineral Fiber Acoustic Ceiling Tile',
                'slug' => 'mineral-fiber-acoustic-ceiling-tile',
                'brand_id' => 'rockfon',
                'category' => 'Acoustic ceiling',
                'summary' => 'Lay-in acoustic tile ceiling for commercial installation and service access.',
                'description' => 'Modular ceiling tile system with exposed grid, acoustic absorption, and easy MEP access.',
                'nrc' => 'Typical 0.60 - 1.00',
                'stc' => 'N/A',
                'fire_rating' => 'Class A options available',
                'specs' => ['Modules' => '600 x 600 mm and 600 x 1200 mm', 'Grid' => 'Exposed T-grid', 'Access' => 'Demountable lay-in tiles'],
            ],
            [
                'name' => 'Standard Drywall Partition',
                'slug' => 'standard-drywall-partition',
                'brand_id' => 'gyproc',
                'category' => 'Drywall partition',
                'summary' => 'Metal stud partition with gypsum board lining and paint-ready finish.',
                'description' => 'Interior non-load-bearing drywall partition for commercial and residential space planning.',
                'nrc' => 'N/A',
                'stc' => 'Assembly dependent',
                'fire_rating' => 'Upgradable with tested fire-rated boards',
                'specs' => ['Studs' => 'C-stud at 400 or 600 mm centers', 'Boards' => 'Single or double layer each side', 'Finish' => 'Jointed and paint-ready'],
            ],
            [
                'name' => 'Rockwool Insulated Acoustic Partition',
                'slug' => 'rockwool-insulated-acoustic-partition',
                'brand_id' => 'usg-boral',
                'category' => 'Soundproofing',
                'summary' => 'Gypsum partition with mineral wool and acoustic perimeter sealing.',
                'description' => 'Noise-control drywall partition combining board mass, cavity absorption, and perimeter sealing.',
                'nrc' => 'N/A',
                'stc' => 'Typical STC 45 - 60 by assembly',
                'fire_rating' => 'Verify tested assembly',
                'specs' => ['Insulation' => 'Rockwool or mineral wool slab', 'Sealant' => 'Acoustic sealant at perimeter and penetrations', 'Critical detail' => 'Avoid rigid sound bridges'],
            ],
            [
                'name' => 'Moisture Resistant Gypsum Board',
                'slug' => 'moisture-resistant-gypsum-board',
                'brand_id' => 'gypsemna',
                'category' => 'Gypsum board',
                'summary' => 'Moisture resistant board for humid interior ceilings and partitions.',
                'description' => 'MR gypsum board for interior areas exposed to intermittent humidity.',
                'nrc' => 'N/A',
                'stc' => 'N/A',
                'fire_rating' => 'Assembly dependent',
                'specs' => ['Core' => 'Moisture resistant gypsum core', 'Facing' => 'Treated paper liner', 'Use' => 'Interior humid zones'],
            ],
        ];
    }

    public function productBySlug(string $slug): ?array
    {
        foreach ($this->products() as $product) {
            if ($product['slug'] === $slug) {
                $product['brand'] = $this->brandById($product['brand_id']);
                return $product;
            }
        }

        return null;
    }

    public function brandById(string $id): ?array
    {
        foreach ($this->brands() as $brand) {
            if ($brand['id'] === $id) {
                return $brand;
            }
        }

        return null;
    }

    public function tutorials(): array
    {
        return [
            [
                'title' => 'Gypsum Ceiling Installation',
                'steps' => ['Site marking', 'Wall angle fixing', 'Suspension rod fixing', 'Main channel fixing', 'Furring channel fixing', 'Board installation', 'Joint treatment', 'Painting'],
            ],
            [
                'title' => 'Drywall Partition Installation',
                'steps' => ['Track installation', 'Stud installation', 'MEP routing', 'Rockwool installation', 'Board fixing', 'Acoustic sealing', 'Joint treatment', 'Finishing'],
            ],
            [
                'title' => 'Acoustic Treatment Review',
                'steps' => ['Room survey', 'Target reverberation selection', 'System selection', 'Reflection control', 'Isolation review', 'Post-install verification'],
            ],
        ];
    }

    public function downloads(): array
    {
        return [
            ['title' => 'Gypsum Ceiling BOQ Template', 'type' => 'XLSX', 'summary' => 'Editable BOQ structure for boards, channels, hangers, screws, jointing, and paint.'],
            ['title' => 'Drywall Partition Method Statement', 'type' => 'PDF', 'summary' => 'Method outline for tracks, studs, boards, rockwool, sealing, and finishing.'],
            ['title' => 'Acoustic Terms Guide', 'type' => 'PDF', 'summary' => 'Reference explaining NRC, STC, reverberation, echo, and absorption.'],
            ['title' => 'Ceiling Grid CAD Detail Placeholder', 'type' => 'CAD', 'summary' => 'CAD library slot for exposed grid, perimeter trim, and suspension details.'],
        ];
    }
}
