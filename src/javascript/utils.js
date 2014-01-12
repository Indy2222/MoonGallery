/*
 * Copyright (C) 2014 Martin Indra <martin.indra at mgn.cz>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var mg = mg || {};
mg.utils = mg.utils || {};

mg.utils.listing = function refreshListing(start, totalCount, onPage) {
    var listing = null,
        page,
        current;

    if (totalCount > onPage) {
        listing = {
            pages: []
        };
        for (var i = 0; i < (totalCount / onPage); i++) {
            page = {
                start: i * onPage,
                number: i
            };
            listing.pages.push(page);

            if (page.start <= start && (page.start + onPage) > start) {
                current = i;
                page.current = true;
            }
        }

        if (current > 0) {
            listing.previous = listing.pages[current - 1];
        }
        if (current < (listing.pages.length - 1)) {
            listing.next = listing.pages[current + 1];
        }
    }

    return listing;
};
